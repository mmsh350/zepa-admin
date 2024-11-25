<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Transaction;
use App\Traits\ActiveUsers;
use App\Traits\KycVerify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    public function show(Request $request)
    {
        // Logged-in User ID
        $loginUserId = Auth::id();

        // Notifications: Fetch unread notifications (limit to 3)
        $notifications = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();

        // Notification Count: Count unread notifications
        $notifyCount = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->count();

        // Filters from the request
        $statusFilter = $request->input('status');
        $referenceFilter = $request->input('reference');
        $serviceTypeFilter = $request->input('service_type');

        // Transactions: Apply filters and paginate
        $transactions = Transaction::query()
            ->when($statusFilter, fn($query) => $query->where('status', $statusFilter))
            ->when($referenceFilter, fn($query) => $query->where('referenceId', 'like', "%$referenceFilter%"))
            ->when($serviceTypeFilter, fn($query) => $query->where('service_type', 'like', "%$serviceTypeFilter%"))
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Check if notifications are enabled for the user
        $notificationsEnabled = Auth::user()->notification ?? false;

        // Pass data to the view
        return view('transaction', compact('transactions', 'notifications', 'notifyCount', 'notificationsEnabled'));
    }



    public function reciept(Request $request)
    {

        $loginUserId = Auth::id();

        // Retrieve the transaction based on the referenceId
        $transaction = Transaction::where('referenceId', $request->referenceId)
            ->first();

        return view('receipt', ['transaction' => $transaction]);
    }
}
