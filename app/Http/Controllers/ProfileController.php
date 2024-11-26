<?php

namespace App\Http\Controllers;

use App\Mail\AccountUpgradeNotification;
use App\Mail\OtpMail;
use App\Mail\PinUpdatedNotification;
use App\Models\Notification;
use App\Models\Services;
use App\Models\Transaction;
use App\Models\Upgrade;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ProfileController extends Controller
{

    protected $loginUserId;

    public function __construct()
    {
        $this->loginUserId = Auth::id();
    }

    public function edit(Request $request)
    {
        // Get the logged-in user ID
        $loginUserId = Auth::id();

        // Fetch unread notifications (limit to 3, sorted by ID descending)
        $notifications = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        // Count unread notifications
        $notifyCount = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->count();

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;


        // Return the view with compact data
        return view('profile.edit', compact(
            'notifications',
            'notifyCount',
            'notificationsEnabled'
        ));
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request)
    {

        $loginUserId = Auth::id();
        // Validate the request
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Retrieve the user
        $user = User::find($loginUserId);

        if (! $user) {

            abort('404');
        }

        // Check the current password
        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Update the password
        $user->password = Hash::make($request->new_password);

        // Save the user
        if ($user->save()) {
            return redirect()->back()->with('status', 'Password updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update password.');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function verifyPin(Request $request)
    {

        // Validate the password
        $request->validate([
            'password' => 'required|string',
        ]);

        // Check if the current password is correct
        if (! Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['error' => 'The provided password is incorrect.'], 422);
        }

        // Rate limiting for OTP requests
        $key = 'otp-request-' . auth()->id();

        if (RateLimiter::tooManyAttempts($key, 3)) {

            $seconds = RateLimiter::availableIn($key);

            return response()->json(['error' => 'Too many OTP requests. Please try again in ' . ceil($seconds / 60) .
                ' minutes.'], 429);
        }

        // Generate a one-time password (OTP)
        $otp = random_int(100000, 999999);
        $request->session()->put('otp', $otp);
        $request->session()->put('otp_expires', now()->addMinutes(5));

        // Send OTP to user's email or phone
        Mail::to(auth()->user()->email)->send(new OtpMail($otp, auth()->user()->first_name));

        // Increment the rate limiter
        RateLimiter::hit($key, 900);

        return response()->json(['success' => true, 'message' => 'OTP has been sent.']);
    }

    public function updatePin(Request $request)
    {
        // Validate the OTP
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        // Check if the OTP matches and hasn't expired
        $storedOtp = $request->session()->get('otp');
        $otpExpires = $request->session()->get('otp_expires');

        if (trim($request->otp) !== trim($storedOtp) || now()->greaterThan($otpExpires)) {
            return response()->json(['error' => 'The provided OTP is invalid or has expired.'], 422);
        }

        // Validate the new PIN
        $request->validate([
            'pin' => 'required|string|min:4|max:4', // Example for a 4-digit PIN
        ]);

        // Check if the user already has a PIN
        $user = auth()->user();

        // Create or update the user's PIN
        $user->pin = bcrypt($request->pin); // Store hashed PIN
        $user->save();

        // Clear the session variables
        $request->session()->forget(['otp', 'otp_expires']);

        Mail::to($user->email)
            ->send(new PinUpdatedNotification($user->first_name));

        return response()->json(['success' => true, 'message' => 'PIN updated successfully.']);
    }

    public function notify(Request $request)
    {
        // Get the logged-in user
        $user = Auth::user();

        // Validate the checkbox value (boolean or null)
        $isEnabled = $request->has('notification_sound');

        // Update user's notification sound preference
        $user->notification = $isEnabled;
        $user->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Notification settings updated successfully!');
    }

    public function upgradeList(Request $request)
    {
        // Get the logged-in user
        $user = Auth::user();

        // Get the search terms from the request
        $searchTerm = $request->input('search');

        // Fetch Notification Data
        $notifications = Notification::where('user_id', $this->loginUserId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        // Count unread notifications
        $notifyCount = Notification::where('user_id', $this->loginUserId)
            ->where('status', 'unread')
            ->count();

        $upgrades = Upgrade::with(['user', 'transaction']) // Eager load user and transaction
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where(function ($query) use ($searchTerm) {
                    // Filter by reference number, status, phone number, or user_name directly from the Upgrade table
                    $query->where('refno', 'like', '%' . $searchTerm . '%')
                        ->orWhere('status', 'like', '%' . $searchTerm . '%')
                        ->orWhere('user_name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('created_at', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('user', function ($query) use ($searchTerm) {
                            $query->where('phone_number', 'like', '%' . $searchTerm . '%');
                        });
                });
            })
            // Order records by status (Pending first) before paginating
            ->orderByRaw("FIELD(status, 'Pending') DESC")
            ->paginate(10, ['*'], 'table1_page');


        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        // Return view with compacted data
        return view('upgrade', compact(
            'upgrades',
            'notifications',
            'notificationsEnabled',
            'notifyCount'
        ));
    }

    public function approveUpgrade(Request $request)
    {
        // Start a database transaction for atomicity
        DB::beginTransaction();

        try {
            // Retrieve the user_id from the request
            $upgradeId = $request->userid;

            // Find the upgrade record by user_id
            $upgrade = Upgrade::where('user_id', $upgradeId)->first();

            // If upgrade is not found, return an error
            if (!$upgrade) {
                abort(404, 'Upgrade record not found');
            }

            // Update the upgrade status
            $upgrade->status = 'Approved';
            $upgrade->save();

            // Update the transaction status if a related transaction exists
            if ($upgrade->transaction) {
                $upgrade->transaction->status = 'Approved';
                $upgrade->transaction->save();
            }

            // Update the user role if a related user exists
            if ($upgrade->user) {
                $upgrade->user->role = 'agent';
                $upgrade->user->save();
            }


            DB::commit();

            //send a mail notification
            $email = $request->email;
            $accountName = User::where('id', $upgradeId)->value('first_name', 'email');

            //Send Mail Notification to admin and user
            $mail_data = [
                'type' => 'Approved',
                'name' => ucwords(strtolower($accountName)),
            ];


            try {
                //Send Mail in response to kyc submitted
                $send = Mail::to($email)->queue(new AccountUpgradeNotification($mail_data));
            } catch (TransportExceptionInterface $e) {
            }


            return response()->json(['status' => 200]);
        } catch (\Exception $e) {

            DB::rollBack();
        }
    }

    public function rejectUpgrade(Request $request)
    {

        // Start a database transaction for atomicity
        DB::beginTransaction();

        try {
            // Retrieve the user_id from the request
            $upgradeId = $request->userid;

            // Find the upgrade record by user_id
            $upgrade = Upgrade::where('user_id', $upgradeId)->first();

            // If upgrade is not found, return an error
            if (!$upgrade) {
                abort(404, 'Upgrade record not found');
            }

            // Update the upgrade status to 'Rejected'
            $upgrade->status = 'Rejected';
            $upgrade->save();

            // Update the transaction status if a related transaction exists
            if ($upgrade->transaction) {
                $upgrade->transaction->status = 'Rejected';
                $upgrade->transaction->save();
            }

            //Refund
            $this->refund($request->userid, $upgrade->transaction->amount);

            // Commit the transaction
            DB::commit();


            //send a mail notification
            $email = $request->email;
            $accountName = User::where('id', $upgradeId)->value('first_name', 'email');

            //Send Mail Notification to admin and user
            $mail_data = [
                'type' => 'Rejected',
                'name' => ucwords(strtolower($accountName)),
            ];


            try {
                //Send Mail in response to kyc submitted
                $send = Mail::to($email)->queue(new AccountUpgradeNotification($mail_data));
            } catch (TransportExceptionInterface $e) {
            }

            return response()->json(['status' => 200]);
        } catch (\Exception $e) {

            DB::rollBack();
        }
    }

    public function refund($user_id, $amount)
    {
        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $user_id)->first();

        $balance = $wallet->balance + $amount;

        $affected = Wallet::where('user_id', $user_id)
            ->update(['balance' => $balance]);

        $referenceno = '';
        srand((float) microtime() * 1000000);
        $data = '123456123456789071234567890890';
        $data .= 'aBCdefghijklmn123opq45rs67tuv89wxyz'; // if you need alphabatic also
        $ddesc = '';
        for ($i = 0; $i < 12; $i++) {
            $referenceno .= substr($data, (rand() % (strlen($data))), 1);
        }

        $payer_name = auth()->user()->first_name . ' ' . Auth::user()->last_name;
        $payer_email = auth()->user()->email;
        $payer_phone = auth()->user()->phone_number;

        Transaction::create([
            'user_id' => $user_id,
            'payer_name' => $payer_name,
            'payer_email' => $payer_email,
            'payer_phone' => $payer_phone,
            'referenceId' => $referenceno,
            'service_type' => 'Upgrade Refund',
            'service_description' => 'Wallet credited with Upgrade Fee of ₦' . number_format($balance, 2),
            'amount' => $amount,
            'gateway' => 'Wallet',
            'status' => 'Approved',
        ]);
    }
}
