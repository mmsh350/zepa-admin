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
}
