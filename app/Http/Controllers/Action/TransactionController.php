<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

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
        $query = Transaction::query()
            ->when($statusFilter, fn($query) => $query->where('status', $statusFilter))
            ->when($referenceFilter, fn($query) => $query->where('referenceId', 'like', "%$referenceFilter%"))
            ->when($serviceTypeFilter, fn($query) => $query->where('service_type', 'like', "%$serviceTypeFilter%"))
            ->when(request('date_from'), fn($query, $dateFrom) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when(request('date_to'), fn($query, $dateTo) => $query->whereDate('created_at', '<=', $dateTo));
        // ->orderBy('id', 'desc')
        // ->paginate(10);

        $transactions = $query->orderBy('id', 'desc')->paginate(10);

        // Calculate total amount
        $total_amount = $query->sum('amount');

        // Check if notifications are enabled for the user
        $notificationsEnabled = Auth::user()->notification ?? false;

        // Pass data to the view
        return view('transaction', compact('transactions', 'notifications', 'notifyCount', 'notificationsEnabled', 'total_amount'));
    }

    public function reciept(Request $request)
    {

        $loginUserId = Auth::id();

        // Retrieve the transaction based on the referenceId
        $transaction = Transaction::where('referenceId', $request->referenceId)
            ->first();

        return view('receipt', ['transaction' => $transaction]);
    }



    public function validatePin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        $userId = auth()->id(); // Get the authenticated user ID
        $rateLimitKey = 'pin-attempts:' . $userId;

        // Check if the user has reached the limit
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $secondsUntilUnlock = RateLimiter::availableIn($rateLimitKey);

            return response()->json([
                'success' => false,
                'message' => 'Too many failed attempts. Please try again after ' . gmdate('i:s', $secondsUntilUnlock) . ' minutes.',
            ]);
        }

        $enteredPin = $request->input('pin');

        if ($this->isPinValid($enteredPin)) {
            // Clear the rate limiter on success
            RateLimiter::clear($rateLimitKey);

            return response()->json([
                'success' => true,
                'message' => 'PIN verified successfully.',
            ]);
        } else {
            // Increment the rate limiter on failure
            RateLimiter::hit($rateLimitKey, 900); // Lockout for 15 minutes

            return response()->json([
                'success' => false,
                'message' => 'Invalid PIN. Please try again.',
            ]);
        }
    }

    private function isPinValid($pin)
    {
        $setPin = auth()->user()->pin; // Retrieve the hashed PIN for the authenticated user

        // Check if the stored hashed PIN is null or empty
        if (is_null($setPin) || empty($setPin)) {
            return false; // If no PIN is set, return false
        }

        // Verify the hashed PIN against the provided PIN
        return Hash::check($pin, $setPin); // Use Laravel's Hash facade to compare
    }
}
