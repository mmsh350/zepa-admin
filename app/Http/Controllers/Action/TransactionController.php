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
    use ActiveUsers;
    use KycVerify;

    public function show(Request $request)
    {
        //Login User Id
        $loginUserId = Auth::id();

        //Check if user is Disabled
        if ($this->is_active() != 1) {
            Auth::logout();

            return view('error');
        }

        //Check KYC status
        $status = $this->is_verified();

        if ($status == 'Pending') {
            return redirect()->route('verification.kyc');
        } elseif ($status == 'Submitted' || $status == 'Rejected') {
            return view('kyc-status')->with(compact('status'));
        } else {
            //Notification Data
            $notifications = Notification::where('user_id', $loginUserId)
                ->orderBy('id', 'desc')
                ->where('status', 'unread')
                ->take(3)
                ->get();

            //Notification Count
            $notifycount = Notification::where('user_id', $loginUserId)
                ->where('status', 'unread')
                ->count();

            // Get filter values from the request
            $statusFilter = $request->input('status');
            $referenceFilter = $request->input('reference');
            $serviceTypeFilter = $request->input('service_type');

            // Get all transactions and apply filters
            $transactions = Transaction::where('user_id', $loginUserId)
                ->when($statusFilter, function ($query, $statusFilter) {
                    return $query->where('status', $statusFilter);
                })
                ->when($referenceFilter, function ($query, $referenceFilter) {
                    return $query->where('referenceId', 'like', "%$referenceFilter%");
                })
                ->when($serviceTypeFilter, function ($query, $serviceTypeFilter) {
                    return $query->where('service_type', 'like', "%$serviceTypeFilter%");
                })
                ->orderBy('id', 'desc')
                ->paginate(10);

            return view('transaction')
                ->with(compact('transactions', 'notifications', 'notifycount'));
        }
    }

    public function reciept(Request $request)
    {

        $loginUserId = Auth::id();

        // Retrieve the transaction based on the referenceId
        $transaction = Transaction::where('referenceId', $request->referenceId)
            ->where('user_id', $loginUserId)
            ->first();

        if (! $transaction) {
            // Handle case when the transaction is not found
            abort(404);
        }

        return view('receipt', ['transaction' => $transaction]);
    }
}
