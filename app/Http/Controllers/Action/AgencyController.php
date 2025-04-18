<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\ACC_Upgrade;
use App\Models\BVNEnrollment;
use App\Models\BVNModification;
use App\Models\CRM_REQUEST;
use App\Models\CRM_REQUEST2;
use App\Models\NIN_SERVICE;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VNIN_TO_NIBSS;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgencyController extends Controller
{

    protected $loginUserId;
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->loginUserId = Auth::id();
        $this->walletService = $walletService;
    }


    // Show CRM
    public function showCRM(Request $request)
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
        $pending = CRM_REQUEST::whereIn('status', ['pending', 'processing'])
            ->count();

        $resolved = CRM_REQUEST::where('status', 'resolved')
            ->count();

        $rejected = CRM_REQUEST::where('status', 'rejected')
            ->count();

        $total_request = CRM_REQUEST::count();

        $query = CRM_REQUEST::with(['user', 'transactions']); // Load related data

        if ($request->filled('search')) { // Check if search input is provided
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('refno', 'like', "%{$searchTerm}%") // Search in Reference No.
                    ->orWhere('bms_ticket_no', 'like', "%{$searchTerm}%") // Search in BMS ID
                    ->orWhere('ticket_no', 'like', "%{$searchTerm}%") // Search in BMS ID
                    ->orWhere('status', 'like', "%{$searchTerm}%") // Search in Status
                    ->orWhereHas('user', function ($subQuery) use ($searchTerm) { // Search in User fields
                        $subQuery->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Check if date_from and date_to are provided and filter accordingly
        if ($dateFrom = request('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom); // Adjust 'created_at' to your date field
        }

        if ($dateTo = request('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo); // Adjust 'created_at' to your date field
        }

        $crm = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'processing' THEN 2
                    ELSE 3
                END
            ") // Prioritize 'pending' first, then 'processing', and others last
            ->orderByDesc('id') // Sort by latest record within the same priority
            ->paginate(10);

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        $request_type = 'crm';

        return view('crm', compact(
            'notifications',
            'pending',
            'resolved',
            'rejected',
            'total_request',
            'crm',
            'notifyCount',
            'notificationsEnabled',
            'request_type'

        ));
    }

    // Display all CRM requests for a user
    public function showRequests($request_id, $type, $requests = null)
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

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        switch ($type) {
            case 'crm2':
                $requests = CRM_REQUEST2::with(['user', 'transactions'])->findOrFail($request_id);
                $request_type = 'crm2';
                break;
            case 'bvn-enrollment':
                $requests = BVNEnrollment::with(['user', 'transactions'])->findOrFail($request_id);
                $request_type = 'bvn-enrollment';
                break;
            case 'bvn-modification':
                $requests = BVNModification::with(['user', 'transactions'])->findOrFail($request_id);
                $request_type = 'bvn-modification';
                break;
            case 'upgrade':
                $requests = ACC_Upgrade::with(['user', 'transactions'])->findOrFail($request_id);
                $request_type = 'upgrade';
                break;

            case 'nin-services':
                $requests = NIN_SERVICE::with(['user', 'transactions'])->findOrFail($request_id);
                $request_type = 'nin-services';
                break;

            case 'vnin-to-nibss':
                $requests = VNIN_TO_NIBSS::with(['user', 'transactions'])->findOrFail($request_id);
                $request_type = 'vnin-to-nibss';
                break;

            default:
                $requests = CRM_REQUEST::with(['user', 'transactions'])->findOrFail($request_id);
                $request_type = 'crm';
        }

        if (strtolower($requests->status) == 'rejected') {
            abort(404, 'Kindly Submit a new request');
        }

        return view(
            'view-request',
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

        switch ($type) {
            case 'crm2':
                $requestDetails = CRM_REQUEST2::findOrFail($id);
                $route = 'crm2';
                break;

            case 'bvn-enrollment':
                $requestDetails = BVNEnrollment::findOrFail($id);
                $route = 'bvn-enrollment';
                $status == 'resolved' ? $status = 'successful' : $request->status;
                break;

            case 'bvn-modification':
                $requestDetails = BVNModification::findOrFail($id);
                $route = 'bvn-modification';
                break;

            case 'upgrade':
                $requestDetails = ACC_Upgrade::findOrFail($id);
                $route = 'account-upgrade';
                break;

            case 'nin-services':
                $requestDetails = NIN_SERVICE::findOrFail($id);
                $route = 'nin-services';
                break;

            case 'vnin-to-nibss':
                $requestDetails = VNIN_TO_NIBSS::findOrFail($id);
                $route = 'vnin-to-nibss';
                break;

            default:
                $requestDetails = CRM_REQUEST::findOrFail($id);
                break;
        }

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

            if ($route == 'bvn-modification'){
                $this->walletService->creditDeveloperWallet($payer_name, $payer_email, $payer_phone, $referenceno . "C2w", "bvn_modification");
            }else if($route == 'nin-services'){
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

            }else{
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

    public function showCRM2(Request $request)
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
        $pending = CRM_REQUEST2::whereIn('status', ['pending', 'processing'])
            ->count();

        $resolved = CRM_REQUEST2::where('status', 'resolved')
            ->count();

        $rejected = CRM_REQUEST2::where('status', 'rejected')
            ->count();

        $total_request = CRM_REQUEST2::count();

        $query = CRM_REQUEST2::with(['user', 'transactions']); // Load related data

        if ($request->filled('search')) { // Check if search input is provided
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('refno', 'like', "%{$searchTerm}%") // Search in Reference No.
                    ->orWhere('phoneno', 'like', "%{$searchTerm}%") // Search in BMS ID
                    ->orWhere('dob', 'like', "%{$searchTerm}%") // Search in BMS ID
                    ->orWhere('status', 'like', "%{$searchTerm}%") // Search in Status
                    ->orWhereHas('user', function ($subQuery) use ($searchTerm) { // Search in User fields
                        $subQuery->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Check if date_from and date_to are provided and filter accordingly
        if ($dateFrom = request('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom); // Adjust 'created_at' to your date field
        }

        if ($dateTo = request('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo); // Adjust 'created_at' to your date field
        }

        $crm = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'processing' THEN 2
                    ELSE 3
                END
            ") // Prioritize 'pending' first, then 'processing', and others last
            ->orderByDesc('id') // Sort by latest record within the same priority
            ->paginate(10);

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        $request_type = 'crm2';

        return view('crm2', compact(
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

    public function showEnrollment(Request $request)
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
        $pending = BVNEnrollment::whereIn('status', ['submitted', 'processing'])
            ->count();

        $resolved = BVNEnrollment::where('status', 'successful')
            ->count();

        $rejected = BVNEnrollment::where('status', 'rejected')
            ->count();

        $total_request = BVNEnrollment::count();

        $query = BVNEnrollment::with(['user', 'transactions']); // Load related data

        if ($request->filled('search')) { // Check if search input is provided
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('refno', 'like', "%{$searchTerm}%")
                    ->orWhere('phone_number', 'like', "%{$searchTerm}%")
                    ->orWhere('type', 'like', "%{$searchTerm}%")
                    ->orWhere('status', 'like', "%{$searchTerm}%")
                    ->orWhere('fullname', 'like', "%{$searchTerm}%");
            });
        }

        // Check if date_from and date_to are provided and filter accordingly
        if ($dateFrom = request('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom); // Adjust 'created_at' to your date field
        }

        if ($dateTo = request('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo); // Adjust 'created_at' to your date field
        }

        $crm = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'submitted' THEN 1
                    WHEN status = 'processing' THEN 2
                    ELSE 3
                END
            ") // Prioritize 'pending' first, then 'processing', and others last
            ->orderByDesc('id') // Sort by latest record within the same priority
            ->paginate(10);

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        $request_type = 'bvn-enrollment';

        return view('bvn-enrollment', compact(
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

    public function showBVN(Request $request)
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
        $pending = BVNModification::whereIn('status', ['pending', 'processing'])
            ->count();

        $resolved = BVNModification::where('status', 'resolved')
            ->count();

        $rejected = BVNModification::where('status', 'rejected')
            ->count();

        $total_request = BVNModification::count();

        $query = BVNModification::with(['user', 'transactions']); // Load related data

        if ($request->filled('search')) { // Check if search input is provided
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('refno', 'like', "%{$searchTerm}%")
                    ->orWhere('enrollment_center', 'like', "%{$searchTerm}%")
                    ->orWhere('bvn_no', 'like', "%{$searchTerm}%")
                    ->orWhere('status', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Check if date_from and date_to are provided and filter accordingly
        if ($dateFrom = request('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom); // Adjust 'created_at' to your date field
        }

        if ($dateTo = request('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo); // Adjust 'created_at' to your date field
        }

        $crm = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'processing' THEN 2
                    ELSE 3
                END
            ") // Prioritize 'pending' first, then 'processing', and others last
            ->orderByDesc('id') // Sort by latest record within the same priority
            ->paginate(10);

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        $request_type = 'bvn-modification';

        return view('bvn-mod', compact(
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

    public function showUpgrade(Request $request)
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
        $pending = ACC_Upgrade::whereIn('status', ['pending', 'processing'])
            ->count();

        $resolved = ACC_Upgrade::where('status', 'resolved')
            ->count();

        $rejected = ACC_Upgrade::where('status', 'rejected')
            ->count();

        $total_request = ACC_Upgrade::count();

        $query = ACC_Upgrade::with(['user', 'transactions']); // Load related data

        if ($request->filled('search')) { // Check if search input is provided
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('refno', 'like', "%{$searchTerm}%")
                    ->orWhere('status', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Check if date_from and date_to are provided and filter accordingly
        if ($dateFrom = request('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom); // Adjust 'created_at' to your date field
        }

        if ($dateTo = request('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo); // Adjust 'created_at' to your date field
        }

        $crm = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'processing' THEN 2
                    ELSE 3
                END
            ") // Prioritize 'pending' first, then 'processing', and others last
            ->orderByDesc('id') // Sort by latest record within the same priority
            ->paginate(10);

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        $request_type = 'upgrade';

        return view('acct-upgrade', compact(
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

    public function ninServices(Request $request)
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
        $pending = NIN_SERVICE::whereIn('status', ['pending', 'processing'])
            ->count();

        $resolved = NIN_SERVICE::where('status', 'resolved')
            ->count();

        $rejected = NIN_SERVICE::where('status', 'rejected')
            ->count();

        $total_request = NIN_SERVICE::count();

        $query = NIN_SERVICE::with(['user', 'transactions']); // Load related data

        if ($request->filled('search')) { // Check if search input is provided
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('refno', 'like', "%{$searchTerm}%")
                    ->orWhere('trackingId', 'like', "%{$searchTerm}%")
                    ->orWhere('status', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if ($dateFrom = request('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = request('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $nin = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'processing' THEN 2
                    ELSE 3
                END
            ") // Prioritize 'pending' first, then 'processing', and others last
            ->orderByDesc('id') // Sort by latest record within the same priority
            ->paginate(10);

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        $request_type = 'nin-services';

        return view('nin-services', compact(
            'notifications',
            'pending',
            'resolved',
            'rejected',
            'total_request',
            'nin',
            'notifyCount',
            'notificationsEnabled',
            'request_type',
        ));
    }

    public function vninToNibss(Request $request)
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
        $pending = VNIN_TO_NIBSS::whereIn('status', ['pending', 'processing'])
            ->count();

        $resolved = VNIN_TO_NIBSS::where('status', 'resolved')
            ->count();

        $rejected = VNIN_TO_NIBSS::where('status', 'rejected')
            ->count();

        $total_request = VNIN_TO_NIBSS::count();

        $query = VNIN_TO_NIBSS::with(['user', 'transactions']); // Load related data

        if ($request->filled('search')) {

            // Check if search input is provided
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('refno', 'like', "%{$searchTerm}%")
                    ->orWhere('bvn_number', 'like', "%{$searchTerm}%")
                     ->orWhere('requestId', 'like', "%{$searchTerm}%")
                    ->orWhere('status', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if ($dateFrom = request('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = request('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $vnin = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'processing' THEN 2
                    ELSE 3
                END
            ") // Prioritize 'pending' first, then 'processing', and others last
            ->orderByDesc('id') // Sort by latest record within the same priority
            ->paginate(10);

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        $request_type = 'vnin-to-nibss';

        return view('vnin-to-nibss', compact(
            'notifications',
            'pending',
            'resolved',
            'rejected',
            'total_request',
            'vnin',
            'notifyCount',
            'notificationsEnabled',
            'request_type',
        ));
    }

    public function viewDocument($id, $type)
    {

        // Determine the request type and fetch the corresponding record
        $request = $type === 'bvn-mod'
            ? BVNModification::findOrFail($id)
            : ACC_Upgrade::findOrFail($id);

        // Get the document path (this should be relative to your external storage URL)
        $documentPath = $request->docs; // Example: 'Documents/1730123905_Daniel2.pdf'

        // Build the full public URL pointing to the external storage location
        $externalUrl = 'https://zepasolutions.com/storage/' . $documentPath;

        // Check if the file exists externally
        // You might want to check if the URL is reachable by performing a HTTP request to check its status
        $headers = get_headers($externalUrl);

        // If the file is not found (404 status)
        if (strpos($headers[0], '404') !== false) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        // Redirect to the external URL for viewing
        return redirect($externalUrl);
    }

    public function viewDocument2($id, $type)
    {

        // Determine the request type and fetch the corresponding record
        $request = $type === 'bvn-mod'
            ? BVNModification::findOrFail($id)
            : ACC_Upgrade::findOrFail($id);

        // Get the document path (this should be relative to your external storage URL)
        $documentPath = $request->docs; // Example: 'Documents/1730123905_Daniel2.pdf'

        // Get the directory path (removes the filename)
        $directoryPath = dirname($documentPath); // Returns 'Documents'

        // Define the backup directory path
        $backupDirectory = $directoryPath . '/backup';


        $newFilePath = $backupDirectory . '/' . basename($documentPath);

        // Build the full public URL pointing to the external storage location
        $externalUrl = 'https://zepasolutions.com/storage/' . $newFilePath;

        // Check if the file exists externally
        // You might want to check if the URL is reachable by performing a HTTP request to check its status
        $headers = get_headers($externalUrl);

        // If the file is not found (404 status)
        if (strpos($headers[0], '404') !== false) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        // Redirect to the external URL for viewing
        return redirect($externalUrl);
    }
    
     public function viewPhotograph($id, $type)
    {

           // Determine the request type and fetch the corresponding record
        $request =  NIN_SERVICE::findOrFail($id);
        // Get the document path (this should be relative to your external storage URL)
        $documentPath = $request->uploads; // Example: 'Documents/1730123905_Daniel2.pdf'

        // Build the full public URL pointing to the external storage location
        $externalUrl = 'https://zepasolutions.com/storage/' . $documentPath;

        // Check if the file exists externally
        // You might want to check if the URL is reachable by performing a HTTP request to check its status
        $headers = get_headers($externalUrl);

        // If the file is not found (404 status)
        if (strpos($headers[0], '404') !== false) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        // Redirect to the external URL for viewing
        return redirect($externalUrl);
    }
}
