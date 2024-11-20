<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Virtual_Accounts;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    protected $loginUserId;

    // Constructor to initialize the property
    public function __construct()
    {
        $this->loginUserId = Auth::id();
    }

    public function claim()
    {

        // Notification Data
        $notifications = Notification::where('user_id', $this->loginUserId)
            ->where('status', 'unread')
            ->latest()
            ->take(3)
            ->get();

        // Notification Count
        $notifyCount = $notifications->count();

        // Retrieve Bonus Information
        $bonus = Bonus::where('user_id', $this->loginUserId)->first();
        $bonus_balance = $bonus->balance;
        $deposit_balance = $bonus->deposit;

        // Retrieve Users with Referral ID and Count of Transactions
        $users = User::where('refferral_id', $this->loginUserId)
            ->withCount('transactions')
            ->paginate(10);

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        // Return View with Data
        return view('claim', compact(
            'users',
            'notifications',
            'bonus_balance',
            'deposit_balance',
            'notifyCount',
            'notificationsEnabled'
        ));

    }

    public function claimBonus($user_id)
    {

        //Claiming Allow
        $users = User::where('id', $user_id)->get();
        $count = 0;
        $claim_id = 0;
        $user_count = 0;

        // Fetch the transaction count for each user
        foreach ($users as $user) {
            $count = $user->transactions()->count();
            $claim_id = $user->claim_id;
        }

        if ($user_id == $this->loginUserId) {
            return redirect()->back()->with('error', 'Nice try! But our system is one step ahead!');
        } elseif ($claim_id == 0 && $count >= 5) {

            $user_count = User::where('refferral_id', $this->loginUserId)
                ->where('claim_id', 0)->count();



            User::where('id', $user_id)->update(['claim_id' => 1]);

            $bonus = Bonus::where('user_id', $this->loginUserId)->first();

            $wallet = Wallet::where('user_id', $this->loginUserId)->first();

            //Divide bonus base on number of users
            $bonus_to_transfer = $bonus->balance / $user_count;

            $new_wallet_balance = $wallet->balance + $bonus_to_transfer;
            $new_deposit_balance = $wallet->deposit + $bonus_to_transfer;

            $new_bonus_balance = $bonus->balance - $bonus_to_transfer;

            Wallet::where('user_id', $this->loginUserId)->update([
                'balance' => $new_wallet_balance,
                'deposit' => $new_deposit_balance]);

            Bonus::where('user_id', $this->loginUserId)->update(['balance' => $new_bonus_balance]);

            $referenceno = '';
            srand((float) microtime() * 1000000);
            $gen = '123456123456789071234567890890';
            $gen .= 'aBCdefghijklmn123opq45rs67tuv89wxyz'; // if you need alphabatic also
            $ddesc = '';
            for ($i = 0; $i < 12; $i++) {
                $referenceno .= substr($gen, (rand() % (strlen($gen))), 1);
            }

            $payer_name = auth()->user()->first_name.' '.Auth::user()->last_name;
            $payer_email = auth()->user()->email;
            $payer_phone = auth()->user()->phone_number;

            Transaction::create([
                'user_id' => $this->loginUserId,
                'payer_name' => $payer_name,
                'payer_email' => $payer_email,
                'payer_phone' => $payer_phone,
                'referenceId' => $referenceno,
                'service_type' => 'Bonus Claim',
                'service_description' => 'Wallet credited with ₦'.number_format($bonus_to_transfer, 2),
                'amount' => $bonus_to_transfer,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);
            //In App Notification
            Notification::create([
                'user_id' => $this->loginUserId,
                'message_title' => 'Bonus Claim',
                'messages' => 'Bonus claim to wallet  ₦'.number_format($bonus_to_transfer, 2),
            ]);

            $successMessage = 'Your bonus has been claimed and added to your main wallet. Congratulations!';

            return redirect()->back()->with('success', $successMessage);
        } else {
            return redirect()->back()->with('error', 'You are not eligible to claim the bonus at this time. Please ensure your referrals have completed the required minimum of 5 transactions to qualify.');
        }

    }

    public function p2p()
    {

        // Notification Data
        $notifications = Notification::where('user_id', $this->loginUserId)
            ->where('status', 'unread')
            ->latest()
            ->take(3)
            ->get();

        // Notification Count
        $notifyCount = $notifications->count();

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        // Return view with the necessary data
        return view('p2p', compact('notifications', 'notifyCount', 'notificationsEnabled'));

    }

    public function transfer(Request $request)
    {

        $request->validate([
            'Wallet_ID' => 'required|numeric|digits:11',
            'Amount' => 'required|numeric|min:500|max:100000',
        ]);

        $exists = User::where('phone_number', $request->Wallet_ID)->exists();

        if ($exists) {

            if (Auth::user()->phone_number == $request->Wallet_ID) {

                return redirect()->back()->with('error', 'Nice try! But our system is one step ahead!');
            }

            $Receiver_details = User::where('phone_number', $request->Wallet_ID)->first();

            if ($Receiver_details->wallet_is_created == 0) {

                return redirect()->back()->with('error', 'Account is pending KYC Verification!');
            }

            //Check if wallet is funded
            $wallet = Wallet::where('user_id', $this->loginUserId)->first();
            $wallet_balance = $wallet->balance;
            $balance = 0;

            if ($wallet_balance < $request->Amount) {
                return redirect()->back()->with('error', 'Sorry Wallet Not Sufficient for Transaction !');
            } else {

                $balance = $wallet->balance - $request->Amount;

                $affected = Wallet::where('user_id', $this->loginUserId)
                    ->update(['balance' => $balance]);

                //Recivers details

                //get reciever wallet id
                $results = User::where('phone_number', $request->Wallet_ID)->first();

                $wallet = Wallet::where('user_id', $results->id)->first();
                $bal = $wallet->balance + $request->Amount;
                $bal2 = $wallet->deposit + $request->Amount;

                Wallet::where('user_id', $results->id)
                    ->update(['balance' => $bal, 'deposit' => $bal2]);

                //Transactions and notifications

                $referenceno = '';
                srand((float) microtime() * 1000000);
                $gen = '123456123456789071234567890890';
                $gen .= 'aBCdefghijklmn123opq45rs67tuv89wxyz'; // if you need alphabatic also
                $ddesc = '';
                for ($i = 0; $i < 12; $i++) {
                    $referenceno .= substr($gen, (rand() % (strlen($gen))), 1);
                } $payer_name = auth()->
                    user()->first_name.' '.Auth::user()->last_name;
                $payer_email = auth()->user()->email;
                $payer_phone = auth()->user()->phone_number;

                Transaction::insert(
                    [[
                        'user_id' => $this->loginUserId,
                        'payer_name' => $Receiver_details->first_name.' '.$Receiver_details->last_name,
                        'payer_email' => $Receiver_details->email,
                        'payer_phone' => $Receiver_details->phone_number,
                        'referenceId' => $referenceno,
                        'service_type' => 'Wallet Transfer',
                        'service_description' => 'Wallet debitted with ₦'.number_format($request->Amount, 2).'
             transferred to
             ('.$Receiver_details->first_name.' '.$Receiver_details->last_name.')',
                        'amount' => $request->Amount,
                        'gateway' => 'Wallet',
                        'status' => 'Approved',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ], [
                        'user_id' => $results->id,
                        'payer_name' => $payer_name,
                        'payer_email' => $payer_email,
                        'payer_phone' => $payer_phone,
                        'referenceId' => $referenceno,
                        'service_type' => 'Wallet Top up',
                        'service_description' => 'Wallet creditted with ₦'.number_format($request->Amount,
                            2).' By ('.$payer_name.')',
                        'amount' => $request->Amount,
                        'gateway' => 'Wallet',
                        'status' => 'Approved',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]]
                );

                //Notifocation
                //In App Notification
                Notification::insert([[
                    'user_id' => $this->loginUserId,
                    'message_title' => 'Wallet Transfer',
                    'messages' => 'Transfer of ₦'.number_format($request->Amount, 2).' was successful',
                ],
                    [
                        'user_id' => $results->id,
                        'message_title' => 'Wallet Top up',
                        'messages' => 'Wallet Credited with ₦'.number_format($request->Amount, 2),
                    ]]);

                $successMessage = 'Transfer Successful';

                // Correctly format the link
                $link = '&nbsp; <a href="'.route('reciept', $referenceno).'"><i class="bi bi-download"></i>
                 Download Receipt</a>';

                return redirect()->back()->with('success', $successMessage.' '.$link);

            }

        } else {
            return redirect()->back()->with('error', 'Sorry Wallet ID does not exist !');
        }

        $successMessage = '';

        return redirect()->back()->with('success', $successMessage);

    }

    public function funding()
    {

        // Notification Data
        $notifications = Notification::where('user_id', $this->loginUserId)
            ->where('status', 'unread')
            ->latest()
            ->take(3)
            ->get();

        // Notification Count
        $notifyCount = $notifications->count();

        // Return all Virtual Accounts
        $virtualAccounts = Virtual_Accounts::where('user_id', $this->loginUserId)
            ->latest()
            ->take(2)
            ->get();

        // Return Wallet and Bonus Balance
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $walletBalance = $wallet->balance;
        $deposit = $wallet->deposit;

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        // Return View with Data
        return view('funding', compact(
            'deposit',
            'walletBalance',
            'virtualAccounts',
            'notifications',
            'notifyCount',
            'notificationsEnabled'
        ));

    }

    public function getReciever(Request $request)
    {

        //  $request->validate([
        //         'walletID' => 'required|numeric|digits:11',
        //     ]);

        $query = User::select([
            DB::raw("CONCAT(first_name, ' ', last_name) AS full_name"),
        ])->where('phone_number', $request->walletID)->get();

        $reciever = $query->first();

        if ($reciever != null) {

            if ($reciever['full_name'] == null) {

                return response()->json('kyc');
            } else {
                return response()->json($reciever['full_name']);
            }

        } else {
            return null;
        }
    }

    public function verify(Request $request)
    {

        $pmethod = $request->pmethod;

        // Paystack Channel
        if ($pmethod == 'paystack') {

            return response()->json(['code' => '201']);

        }
        //Monie Point Channel
        elseif ($pmethod == 'moniepoint') {

            $link = route('reciept', $request->ref);

            return response()->json(['code' => '200', 'link' => $link]);
        }
    }
}
