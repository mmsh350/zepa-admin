<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $request->validate([
            'status' => 'required|string',
            'comment' => 'required|string',
        ]);

        $route = 'crm';
        $requestDetails = null;
        $status = $request->status;


        $requestDetails = CRM_REQUEST::findOrFail($id);


        $requestDetails->status = $status;
        $requestDetails->reason = $request->comment;

        $payer_name = auth()->user()->first_name . ' ' . Auth::user()->last_name;
        $payer_email = auth()->user()->email;
        $payer_phone = auth()->user()->phone_number;

        $referenceno = '';
        srand((float) microtime() * 1000000);
        $gen = '123456123456789071234567890890';
        $gen .= 'aBCdefghijklmn123opq45rs67tuv89wxyz';
        $ddesc = '';
        for ($i = 0; $i < 12; $i++) {
            $referenceno .= substr($gen, (rand() % (strlen($gen))), 1);
        }


        if ($request->status === 'resolved') {

            if ($route == 'bvn-modification') {
                $this->walletService->creditDeveloperWallet($payer_name, $payer_email, $payer_phone, $referenceno . "C2w", "bvn_modification");
            } else if ($route == 'nin-services') {
                if ($requestDetails->service_type) {
                    $serviceTypeMap = [
                        'Date of Birth Update' => 'nin_modification_dob',
                        'Name Modification' => 'nin_modification_name',
                        'Change of Address' => 'nin_modification_address',
                        'Phone Number Update' => 'nin_modification_phone',
                    ];

                    $service_key = $serviceTypeMap[$requestDetails->service_type] ?? 'nin_modification_general';

                    $this->walletService->creditDeveloperWallet(
                        $payer_name,
                        $payer_email,
                        $payer_phone,
                        $referenceno . "C2w",
                        $service_key
                    );
                }
            } else {
                //do nothing
            }
        }

        if ($request->status === 'rejected') {

            $refundAmount = $request->refundAmount;

            $wallet = Wallet::where('user_id', $requestDetails->user_id)->first();

            $balance = $wallet->balance + $refundAmount;

            Wallet::where('user_id', $requestDetails->user_id)
                ->update(['balance' => $balance]);


            Transaction::create([
                'user_id' => $requestDetails->user_id,
                'payer_name' => $payer_name,
                'payer_email' => $payer_email,
                'payer_phone' => $payer_phone,
                'referenceId' => $referenceno,
                'service_type' => 'Agency Refund',
                'service_description' => 'Wallet credited with a Request fee of ' . number_format($refundAmount, 2),
                'amount' => $refundAmount,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);

            //In App Notification
            Notification::create([
                'user_id' => $requestDetails->user_id,
                'message_title' => 'Agency Refund',
                'messages' => 'Wallet credited with a Request fee of ' . number_format($refundAmount, 2),
            ]);
        }

        $requestDetails->save();

        return redirect()->route($route)->with('success', 'Request status updated successfully.');
    }
}
