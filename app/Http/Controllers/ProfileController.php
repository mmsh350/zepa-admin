<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Services;
use App\Models\Transaction;
use App\Models\Upgrade;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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

    public function upgrade(Request $request)
    {

        if ($request->type != 'agent') {
            return response()->json([
                'message' => 'Invalid Request Type',
                'errors' => ['Invalid Request Type' => 'Request Not Allowed'],
            ], 422);
        } else {

            $loginUserId = Auth::id();
            // Services Fee
            $ServiceFee = 0;
            $ServiceFee = Services::where('service_code', '105')->first();
            $ServiceFee = $ServiceFee->amount;

            //Notification Count
            $count = 0;
            $count = Upgrade::where('user_id', $loginUserId)->count();

            if ($count > 0) {
                return response()->json([
                    'message' => 'Error',
                    'errors' => ['Account Upgrade' => 'We are reviewing your request. will get back to you. if your request is not successfull you will be refunded'],
                ], 422);
            }

            //Check if wallet is funded
            $wallet = Wallet::where('user_id', $loginUserId)->first();
            $wallet_balance = $wallet->balance;
            $balance = 0;

            if ($wallet_balance < $ServiceFee) {
                return response()->json([
                    'message' => 'Error',
                    'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
                ], 422);
            } else {
                $balance = $wallet->balance - $ServiceFee;

                $affected = Wallet::where('user_id', $loginUserId)
                    ->update(['balance' => $balance]);

                $referenceno = '';
                srand((float) microtime() * 1000000);
                $data = '123456123456789071234567890890';
                $data .= 'aBCdefghijklmn123opq45rs67tuv89wxyz'; // if you need alphabatic also
                $ddesc = '';
                for ($i = 0; $i < 12; $i++) {
                    $referenceno .= substr($data, (rand() % (strlen($data))), 1);
                }

                $payer_name = auth()->user()->first_name.' '.Auth::user()->last_name;
                $payer_email = auth()->user()->email;
                $payer_phone = auth()->user()->phone_number;

                $transaction = Transaction::create([
                    'user_id' => $loginUserId,
                    'payer_name' => $payer_name,
                    'payer_email' => $payer_email,
                    'payer_phone' => $payer_phone,
                    'referenceId' => $referenceno,
                    'service_type' => 'Account Update Request',
                    'service_description' => 'Wallet debitted with Upgrade Fee of ₦'.number_format($ServiceFee, 2),
                    'amount' => $ServiceFee,
                    'gateway' => 'Wallet',
                    'status' => 'Pending',
                ]);

                $txnId = $transaction->id;
                $refno = $transaction->referenceId;
                //Submit the request

                Upgrade::create([
                    'user_id' => $loginUserId,
                    'user_name' => $payer_name,
                    'tnx_id' => $txnId,
                    'refno' => $refno,
                    'type' => 'Agent Upgrade',
                    'status' => 'Pending',
                ]);

                return response()->json(['status' => 200, 'msg' => 'Upgrade Request Submitted']);

            }

        }

    }
}
