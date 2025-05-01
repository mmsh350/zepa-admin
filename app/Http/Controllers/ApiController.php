<?php

namespace App\Http\Controllers;

use App\Models\ApiWithdrawal;
use App\Models\Notification;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    protected $loginUserId;
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->loginUserId = Auth::id();
        $this->walletService = $walletService;
    }

    public function index(Request $request)
    {

        $userId = $this->loginUserId;

        // Notification Data
        $notifications = Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        // Notification Count
        $notifyCount = Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->count();

        // CRM Request Data


        // CRM Request Data
        $pending = DB::connection('mysql_second')
            ->table('bvn_enrollments')
            ->whereIn('status', ['submitted', 'processing'])
            ->count();

        $resolved = DB::connection('mysql_second')
            ->table('bvn_enrollments')
            ->where('status', 'successful')
            ->count();

        $rejected = DB::connection('mysql_second')
            ->table('bvn_enrollments')
            ->where('status', 'rejected')
            ->count();

        $total_request = DB::connection('mysql_second')
            ->table('bvn_enrollments')
            ->count();


        $query = DB::connection('mysql_second')
            ->table('bvn_enrollments');

        // Search Filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('refno', 'like', "%{$searchTerm}%")
                    ->orWhere('phone_number', 'like', "%{$searchTerm}%")
                    ->orWhere('status', 'like', "%{$searchTerm}%")
                    ->orWhere('fullname', 'like', "%{$searchTerm}%");
            });
        }

        // Date Filters
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Ordering + Pagination
        $crm = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'submitted' THEN 1
                    WHEN status = 'processing' THEN 2
                    ELSE 3
                END
            ")
            ->orderByDesc('id')
            ->paginate(10);

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        $request_type = 'bvn-enrollment';

        return view('api-bvn-enrollment', compact(
            'notifications',
            'pending',
            'resolved',
            'rejected',
            'total_request',
            'crm',
            'notifyCount',
            'notificationsEnabled',
            'request_type',
        ));
    }

    public function showRequests($request_id, $type, $requests = null)
    {


        // Notification Data
        $notifications = Notification::where('user_id', $this->loginUserId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        // Notification Count
        $notifyCount = Notification::where('user_id',  $this->loginUserId)
            ->where('status', 'unread')
            ->count();

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        $requests = DB::connection('mysql_second')
            ->table('bvn_enrollments')
            ->where('id', $request_id)
            ->first();

        $request_type = 'bvn-enrollment';


        if (strtolower($requests->status) == 'rejected') {
            abort(404, 'Kindly Submit a new request');
        }

        return view(
            'api-view-request',
            compact(
                'requests',
                'notifications',
                'notifyCount',
                'notificationsEnabled',
                'request_type'
            )
        );
    }

    public function updateRequestStatus(Request $request, $id, $type)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'comment' => 'required|string',
            'url' => 'nullable|string'
        ]);

        $url = $validated['url'];

        $connection = DB::connection('mysql_second');
        $table = $connection->table('bvn_enrollments');

        $requestDetails = $table->where('id', $id)->first();

        if (!$requestDetails) {
            abort(404, 'Request not found.');
        }

        $table->where('id', $id)->update([
            'status' => $validated['status'],
            'reason' => $validated['comment'],
        ]);

        if (!empty($url)) {
            try {
                Http::post($url, [
                    'status' => $validated['status'],
                    'reason' => $validated['comment'],
                    'refno' => $request->refno,
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to post status update: " . $e->getMessage());
            }
        }

        return redirect()
            ->route('api.enrollment')
            ->with('success', 'Request status updated successfully.');
    }

    public function process(Request $request){

         try {
                $devID = config('wallet.developer_api_id');
                $connection = DB::connection('mysql_second');
                $table = $connection->table('wallets');

                // Begin transaction
                $connection->beginTransaction();

                // Lock the row to prevent race conditions
                $wallet = $table->where('user_id', $devID)->lockForUpdate()->first();

                if (!$wallet) {
                    return redirect()->route('api.withdrawal')->with('error', 'Wallet not found.');
                }

                $amount = $wallet->naira_balance;

                if ($amount <= 0) {
                    return redirect()->route('api.withdrawal')->with('error', 'No balance to withdraw.');
                }

                // Move the funds (your service logic)
                $this->walletService->moveToDeveloperWallet($amount);

                // Update the wallet balance
                $table->where('user_id', $devID)->update([
                    'naira_balance' => $wallet->naira_balance - $amount,
                ]);

                ApiWithdrawal::create([
                    'user_id' => auth()->id(),
                    'amount' => $amount,
                    'description' => 'Moved balance to main wallet', // your own message
                ]);

                // Commit the transaction
                $connection->commit();

                return redirect()->route('api.withdrawal')->with('success', 'Successful withdrawal');

                } catch (\Throwable $e) {
                    // Rollback on error
                    if (isset($connection)) {
                        $connection->rollBack();
                    }

                    // Optional: log the error
                    Log::error('Wallet withdrawal failed: ' . $e->getMessage());

                    return redirect()->route('api.withdrawal')->with('error', 'Something went wrong. Please try again.');
                }

    }
    public function history(){

        $userId = $this->loginUserId;

        // Notification Data
        $notifications = Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        // Notification Count
        $notifyCount = Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->count();

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        $history =ApiWithdrawal::paginate(10);

        return view('api-withdrawal', compact(
                'notifications',
                'notifyCount',
                'notificationsEnabled',
                'history',
            ));

    }
}
