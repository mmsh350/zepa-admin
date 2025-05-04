<?php

namespace App\Http\Controllers;

use App\Models\ApiWithdrawal;
use App\Models\Notification;
use App\Models\UserEnrollment;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

    public function showUsers(Request $request)
    {
        $userId = $this->loginUserId;

        // Notifications
        $notifications = Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        $notifyCount = Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->count();

        $notificationsEnabled = Auth::user()->notification;

        // Filters
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $usersQuery = DB::connection('mysql_second')
            ->table('users')
            ->leftJoin('wallets', 'users.id', '=', 'wallets.user_id')
            ->where('users.usertype', 'user');

        // Search filter
        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('users.name', 'like', "%$search%")
                 ->orWhere('users.phone_number', 'like', "%$search%")
                    ->orWhere('users.email', 'like', "%$search%");
            });
        }

        $users = $usersQuery->select(
                'users.*',
                'wallets.naira_balance as naira_balance',
                'wallets.nin_balance as nin_balance',
                'wallets.bvn_balance as bvn_balance'
            )->paginate($perPage)->withQueryString();

        return view('api-user-index', compact(
            'notifications',
            'notifyCount',
            'notificationsEnabled',
            'users'
        ));
    }

    public function activate($user_id)
    {
        // Fetch the user
        $user = DB::connection('mysql_second')
            ->table('users')
            ->where('id', $user_id)
            ->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        // Toggle is_active
        $newStatus = !$user->is_active;

        // Update user
        DB::connection('mysql_second')
            ->table('users')
            ->where('id', $user_id)
            ->update(['is_active' => $newStatus]);

        return back()->with('success', 'User activation status updated.');
    }

    // public function ShowUpload(){

    //     $userId = $this->loginUserId;

    //     // Notifications
    //     $notifications = Notification::where('user_id', $userId)
    //         ->where('status', 'unread')
    //         ->orderByDesc('id')
    //         ->take(3)
    //         ->get();

    //     $notifyCount = Notification::where('user_id', $userId)
    //         ->where('status', 'unread')
    //         ->count();

    //     $notificationsEnabled = Auth::user()->notification;

    //     $data ="";

    //      return view('api-enrollments-upload', compact(
    //         'notifications',
    //         'notifyCount',
    //         'notificationsEnabled',
    //         'data'
    //     ));

    // }

    public function ShowUpload(Request $request)
{
    $userId = $this->loginUserId;

    // Notifications
    $notifications = Notification::where('user_id', $userId)
        ->where('status', 'unread')
        ->orderByDesc('id')
        ->take(3)
        ->get();

    $notifyCount = Notification::where('user_id', $userId)
        ->where('status', 'unread')
        ->count();

    $notificationsEnabled = Auth::user()->notification;

    // Filter logic
    $query = UserEnrollment::query();

    // Filter by ticket_number
    if ($request->has('ticket_number') && $request->ticket_number != '') {
        $query->where('ticket_number', 'like', '%' . $request->ticket_number . '%');
    }

    if ($request->has('bvn') && $request->bvn != '') {
        $query->where('bvn', 'like', '%' . $request->bvn . '%');
    }

    $data = $query->paginate(10); // You can adjust the pagination as needed

    return view('api-enrollments-upload', compact(
        'notifications',
        'notifyCount',
        'notificationsEnabled',
        'data'
    ));
}

// public function uploadCsv(Request $request)
// {
//     // Validate the file
//     $validator = Validator::make($request->all(), [
//         'csv_file' => 'required|mimes:csv,txt|max:2048',
//     ]);

//     if ($validator->fails()) {
//         return redirect()->back()->withErrors($validator)->withInput();
//     }

//     $file = $request->file('csv_file');
//     $path = $file->getRealPath();
//     $data = array_map('str_getcsv', file($path));

//     // Remove header if present
//     if (isset($data[0]) && str_contains(strtolower(implode('', $data[0])), 'ticket')) {
//         unset($data[0]);
//     }

//     $successfulInserts = 0;
//     $unsuccessfulInserts = 0;
//     $malformedRows = [];

//     // Process each row
//     foreach ($data as $index => $row) {
//         // Ensure each row has at least 13 columns, and if not, append null for missing columns
//         while (count($row) < 13) {
//             $row[] = null; // Append null for any missing columns
//         }

//         // Clean helper (removes extra quotes and spaces)
//         $clean = fn($val) => trim(str_replace(['"', "'"], '', $val));

//         // Parse dates safely
//         $captureDate = $clean($row[10]);
//         $syncDate = $clean($row[11]);
//         $validationDate = $clean($row[12]);

//         // Ensure date format is consistent for parsing
//         try {
//             if (strpos($captureDate, 'Thu') !== false) {
//                 $captureDate = Carbon::createFromFormat('D M d Y H:i:s T', $captureDate)->toDateString();
//             }
//         } catch (\Exception $e) {
//             $captureDate = null;
//         }

//         try {
//             if (strpos($syncDate, 'Thu') !== false) {
//                 $syncDate = Carbon::createFromFormat('D M d Y H:i:s T', $syncDate)->toDateString();
//             }
//         } catch (\Exception $e) {
//             $syncDate = null;
//         }

//         try {
//             if (strpos($validationDate, 'Thu') !== false) {
//                 $validationDate = Carbon::createFromFormat('D M d Y H:i:s T', $validationDate)->toDateString();
//             }
//         } catch (\Exception $e) {
//             $validationDate = null;
//         }

//         // Check if validation message exists, otherwise append null
//         $validationMessage = !empty($row[9]) ? $clean($row[9]) : null;

//         // Check if the ticket_number already exists in the database
//         $existingRecord = DB::table('user_enrollments')->where('ticket_number', $clean($row[0]))->first();

//         if ($existingRecord) {
//             // Update the existing record
//             DB::table('user_enrollments')
//                 ->where('ticket_number', $clean($row[0]))
//                 ->update([
//                     'bvn'                => $clean($row[1]),
//                     'agt_mgt_inst_name'  => $clean($row[2]),
//                     'agt_mgt_inst_code'  => $clean($row[3]),
//                     'agent_name'         => $clean($row[4]),
//                     'agent_code'         => $clean($row[5]),
//                     'enroller_code'      => $clean($row[6]),
//                     'bms_import_id'      => $clean($row[7]),
//                     'validation_status'  => $clean($row[8]),
//                     'validation_message' => $validationMessage,
//                     'capture_date'       => $captureDate,
//                     'sync_date'          => $syncDate,
//                     'validation_date'    => $validationDate,
//                     'updated_at'         => now(),
//                 ]);
//             $successfulInserts++;
//         } else {
//             // Insert new record
//             DB::table('user_enrollments')->insert([
//                 'ticket_number'      => $clean($row[0]),
//                 'bvn'                => $clean($row[1]),
//                 'agt_mgt_inst_name'  => $clean($row[2]),
//                 'agt_mgt_inst_code'  => $clean($row[3]),
//                 'agent_name'         => $clean($row[4]),
//                 'agent_code'         => $clean($row[5]),
//                 'enroller_code'      => $clean($row[6]),
//                 'bms_import_id'      => $clean($row[7]),
//                 'validation_status'  => $clean($row[8]),
//                 'validation_message' => $validationMessage,
//                 'capture_date'       => $captureDate,
//                 'sync_date'          => $syncDate,
//                 'validation_date'    => $validationDate,
//                 'created_at'         => now(),
//                 'updated_at'         => now(),
//             ]);
//             $successfulInserts++;
//         }
//     }

//     // Log malformed rows if any
//     if (!empty($malformedRows)) {
//         Log::warning('Malformed rows found during CSV upload:', $malformedRows);
//     }

//     // Log the count of successful and unsuccessful inserts
//     Log::info("CSV upload completed. Successful inserts: $successfulInserts, Unsuccessful inserts: $unsuccessfulInserts");

//     return back()->with('success', 'CSV uploaded and data processed successfully.');
// }

public function uploadCsv(Request $request)
{
    $validator = Validator::make($request->all(), [
        'csv_file' => 'required|mimes:csv,txt|max:2048',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $file = $request->file('csv_file');
    $path = $file->getRealPath();
    $data = array_map('str_getcsv', file($path));

    if (isset($data[0]) && str_contains(strtolower(implode('', $data[0])), 'ticket')) {
        unset($data[0]);
    }

    $successfulInserts = 0;
    $unsuccessfulInserts = 0;
    $malformedRows = [];

    foreach ($data as $index => $row) {
        while (count($row) < 18) {
            $row[] = null;
        }

        $clean = fn($val) => trim(str_replace(['"', "'"], '', $val));

        try {
            $captureDate = strpos($row[14], 'Thu') !== false
                ? Carbon::createFromFormat('D M d Y H:i:s T', $row[14])->toDateString()
                : $clean($row[14]);
        } catch (\Exception $e) {
            $captureDate = null;
        }

        try {
            $syncDate = strpos($row[15], 'Thu') !== false
                ? Carbon::createFromFormat('D M d Y H:i:s T', $row[15])->toDateString()
                : $clean($row[15]);
        } catch (\Exception $e) {
            $syncDate = null;
        }

        try {
            $validationDate = strpos($row[16], 'Thu') !== false
                ? Carbon::createFromFormat('D M d Y H:i:s T', $row[16])->toDateString()
                : $clean($row[16]);
        } catch (\Exception $e) {
            $validationDate = null;
        }

        $ticketNumber = $clean($row[0]);
        $existingRecord = DB::table('user_enrollments')->where('ticket_number', $ticketNumber)->first();

        $payload = [
            'ticket_number'        => $ticketNumber,
            'bvn'                  => $clean($row[1]),
            'agt_mgt_inst_name'    => $clean($row[2]),
            'agt_mgt_inst_code'    => $clean($row[3]),
            'agent_name'           => $clean($row[4]),
            'agent_code'           => $clean($row[5]),
            'enroller_code'        => $clean($row[6]),
            'latitude'             => $clean($row[7]),
            'longitude'            => $clean($row[8]),
            'finger_print_scanner' => $clean($row[9]),
            'bms_import_id'        => $clean($row[10]),
            'validation_status'    => $clean($row[11]),
            'validation_message'   => $clean($row[12]),
            'amount'               => $clean($row[13]),
            'capture_date'         => $captureDate,
            'sync_date'            => $syncDate,
            'validation_date'      => $validationDate,
            'updated_at'           => now(),
        ];

        if ($existingRecord) {
            DB::table('user_enrollments')->where('ticket_number', $ticketNumber)->update($payload);
        } else {
            $payload['created_at'] = now();
            DB::table('user_enrollments')->insert($payload);
        }

        $successfulInserts++;
    }

    if (!empty($malformedRows)) {
        Log::warning('Malformed rows found during CSV upload:', $malformedRows);
    }

    Log::info("CSV upload completed. Successful inserts: $successfulInserts, Unsuccessful inserts: $unsuccessfulInserts");

    return back()->with('success', 'CSV uploaded and data processed successfully.');
}


}
