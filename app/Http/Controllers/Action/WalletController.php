<?php

namespace App\Http\Controllers\Action;

use App\Helpers\noncestrHelper;
use App\Helpers\signatureHelper;
use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Notification;
use App\Models\Services;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Virtual_Accounts;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                'deposit' => $new_deposit_balance
            ]);

            Bonus::where('user_id', $this->loginUserId)->update(['balance' => $new_bonus_balance]);

            $referenceno = '';
            srand((float) microtime() * 1000000);
            $gen = '123456123456789071234567890890';
            $gen .= 'aBCdefghijklmn123opq45rs67tuv89wxyz'; // if you need alphabatic also
            $ddesc = '';
            for ($i = 0; $i < 12; $i++) {
                $referenceno .= substr($gen, (rand() % (strlen($gen))), 1);
            }

            $payer_name = auth()->user()->first_name . ' ' . Auth::user()->last_name;
            $payer_email = auth()->user()->email;
            $payer_phone = auth()->user()->phone_number;

            Transaction::create([
                'user_id' => $this->loginUserId,
                'payer_name' => $payer_name,
                'payer_email' => $payer_email,
                'payer_phone' => $payer_phone,
                'referenceId' => $referenceno,
                'service_type' => 'Bonus Claim',
                'service_description' => 'Wallet credited with ₦' . number_format($bonus_to_transfer, 2),
                'amount' => $bonus_to_transfer,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);
            //In App Notification
            Notification::create([
                'user_id' => $this->loginUserId,
                'message_title' => 'Bonus Claim',
                'messages' => 'Bonus claim to wallet  ₦' . number_format($bonus_to_transfer, 2),
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

    public function transferP2p(Request $request)
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
                }
                $payer_name = auth()->user()->first_name . ' ' . Auth::user()->last_name;
                $payer_email = auth()->user()->email;
                $payer_phone = auth()->user()->phone_number;

                Transaction::insert(
                    [[
                        'user_id' => $this->loginUserId,
                        'payer_name' => $Receiver_details->first_name . ' ' . $Receiver_details->last_name,
                        'payer_email' => $Receiver_details->email,
                        'payer_phone' => $Receiver_details->phone_number,
                        'referenceId' => $referenceno,
                        'service_type' => 'Wallet Transfer',
                        'service_description' => 'Wallet debitted with ₦' . number_format($request->Amount, 2) . '
             transferred to
             (' . $Receiver_details->first_name . ' ' . $Receiver_details->last_name . ')',
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
                        'service_description' => 'Wallet creditted with ₦' . number_format(
                            $request->Amount,
                            2
                        ) . ' By (' . $payer_name . ')',
                        'amount' => $request->Amount,
                        'gateway' => 'Wallet',
                        'status' => 'Approved',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]]
                );

                //Notifocation
                //In App Notification
                Notification::insert([
                    [
                        'user_id' => $this->loginUserId,
                        'message_title' => 'Wallet Transfer',
                        'messages' => 'Transfer of ₦' . number_format($request->Amount, 2) . ' was successful',
                    ],
                    [
                        'user_id' => $results->id,
                        'message_title' => 'Wallet Top up',
                        'messages' => 'Wallet Credited with ₦' . number_format($request->Amount, 2),
                    ]
                ]);

                $successMessage = 'Transfer Successful';

                // Correctly format the link
                $link = '&nbsp; <a href="' . route('reciept', $referenceno) . '"><i class="bi bi-download"></i>
                 Download Receipt</a>';

                return redirect()->back()->with('success', $successMessage . ' ' . $link);
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

    public function showPayout()
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

        // Return Wallet and Bonus Balance
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $walletBalance = number_format($wallet->balance, 2);

        $getCharges = Services::where('service_code', '150')
            ->where('category', 'charges')
            ->where('type', 'PAYOUT')
            ->first();

        $charges = $getCharges->amount;

        // Return view with the necessary data
        return view('payout', compact('notifications', 'notifyCount', 'notificationsEnabled', 'walletBalance', 'charges'));
    }

    public function isServiceEnabled($serviceId)
    {

        $serviceStatus = DB::table('service_statuses')->where('service_id', $serviceId)->first();

        if ($serviceStatus && $serviceStatus->is_enabled) {
            return true;
        }

        return false;
    }

    public function transactionLimitCheck($amount)
    {

        //Current User
        $userId = Auth::id();

        // Get current date for daily transfers
        $currentDate = Carbon::now()->format('Y-m-d');

        // Count transfers for the user on the current date
        $dailyTransferAmount = DB::table('user_transactions')
            ->where('user_id', $userId)
            ->whereDate('transaction_date', $currentDate)
            ->where('transaction_type', 'transfer')
            ->sum('amount');

        // Get user's daily limit for transfers
        $user = User::find($userId);
        $dailyLimit = $user->daily_limit;

        // Compare the total daily transaction amount with the user's daily limit
        if (($dailyTransferAmount) + $amount > $dailyLimit) {
            return false;
        }

        return true;
    }

    public function checkWalletBalance($amount)
    {
        $userId = Auth::id();

        $wallet = Wallet::where('user_id', $userId)->first();

        if (! $wallet) {
            return ['status' => false, 'message' => 'Wallet not found.'];
        }

        $wallet_balance = $wallet->balance;

        if ($wallet_balance < $amount) {
            return ['status' => false, 'message' => 'Sorry, Wallet not sufficient for transaction!'];
        }

        return ['status' => true, 'balance' => $wallet_balance];
    }

    public function payout(Request $request)
    {
        // Request validation checks with custom messages
        $request->validate(
            [
                'amount' => 'required|numeric|min:10',
                'bankCode' => 'required|numeric|digits:6',
                'accountNumber' => 'required|numeric|min:10',
            ],
            [
                'amount.required' => 'Please enter the amount to transfer.',
                'amount.numeric' => 'The amount must be a valid number.',
                'amount.min' => 'The minimum transfer amount is 50 Naira.',
                'bankCode.required' => 'Please select a bank code.',
                'bankCode.numeric' => 'The bank code must be a valid number.',
                'bankCode.digits' => 'The bank code must be exactly 6 digits.',
                'accountNumber.required' => 'Please enter an account number.',
                'accountNumber.numeric' => 'The account number must be a valid number.',
                'accountNumber.min' => 'The account number must be at least 10 digits.',
            ]
        );

        $amount = $request->amount;

        $getCharges = Services::where('service_code', '150')
            ->where('category', 'charges')
            ->where('type', 'PAYOUT')
            ->first();

        $charges = $getCharges->amount;

        //Check if payout is enbaled
        if (! $this->isServiceEnabled(1)) {
            return redirect()->back()->with('error', 'Sorry, bank transfers are temporarily unavailable. Please try again later.');
        }

        $final_amount = $amount + $charges;
        //Transaction Limit Check
        if (! $this->transactionLimitCheck($final_amount)) {

            return redirect()->back()->with('error', 'You\'ve reached your daily transfer limit. Try again tomorrow.');
        }

        //Check if account balance is sufficent
        $walletCheck = $this->checkWalletBalance($final_amount);

        if (! $walletCheck['status']) {
            return redirect()->back()->with('error', $walletCheck['message']);
        }

        $walletBalance = $walletCheck['balance'];

        $payer_name = auth()->user()->first_name . ' ' . Auth::user()->last_name;
        $payer_phone = auth()->user()->phone_number;
        $payer_email = auth()->user()->email;
        $payer_phone = auth()->user()->phone_number;

        $amount_to_post = $amount * 100;

        try {

            $requestTime = (int) (microtime(true) * 1000);

            $noncestr = noncestrHelper::generateNonceStr();
            $orderId = noncestrHelper::generateOrderId();

            $data = [
                'requestTime' => $requestTime,
                'version' => env('VERSION'),
                'nonceStr' => $noncestr,
                'orderId' => $orderId,
                'payeeName' => $payer_name,
                'payeeBankCode' => $request->bankCode,
                'payeeBankAccNo' => $request->accountNumber,
                'payeePhoneNo' => $payer_phone,
                'amount' => $amount_to_post,
                'currency' => 'NGN',
                'notifyUrl' => env('NOTIFY_URL'),
            ];
            if ($request->filled('reference')) {
                $data['remark'] = $request->reference;
            }

            $signature = signatureHelper::generate_signature($data, config('keys.private'));

            $url = env('BASE_URL3') . 'api/v2/merchant/payment/payout';
            $token = env('BEARER_TOKEN');
            $headers = [
                'Accept: application/json, text/plain, */*',
                'CountryCode: NG',
                "Authorization: Bearer $token",
                "Signature: $signature",
                'Content-Type: application/json',
            ];

            // Initialize cURL
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            // Execute request
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                throw new \Exception('cURL Error: ' . curl_error($ch));
            }

            // Close cURL session
            curl_close($ch);

            // Decode the JSON response to an associative array
            $response = json_decode($response, true);

            // Check if decoding was successful
            if ($response === null) {
                return redirect()->back()->with('error', 'Failed Invalid response.');
            }

            // Check for success
            if (isset($response['respCode']) && $response['respCode'] === '00000000') {
                if (isset($response['data']['status']) && $response['data']['status'] === 1) {

                    $referenceno = $response['data']['orderId'];

                    Transaction::create([
                        'user_id' => $this->loginUserId,
                        'payer_name' => $payer_name,
                        'payer_email' => $payer_email,
                        'payer_phone' => $payer_phone,
                        'referenceId' => $referenceno,
                        'service_type' => 'Payout',
                        'service_description' => "Wallet Payout to {$request->accountNumber}" . ($request->reference ? "|desc|{$request->reference}" : "|NA"),
                        'amount' => $final_amount,
                        'gateway' => 'Wallet',
                        'status' => 'Pending',
                    ]);

                    //In App Notification
                    Notification::create([
                        'user_id' => $this->loginUserId,
                        'message_title' => 'Payout',
                        'messages' => 'Payout from wallet  ₦' . number_format($final_amount, 2),
                    ]);

                    //update user transaction count
                    DB::table('user_transactions')->insert([
                        'user_id' => $this->loginUserId,
                        'amount' => $final_amount,
                        'transaction_date' => Carbon::now()->format('Y-m-d'),
                        'transaction_type' => 'transfer',
                    ]);

                    $successMessage = 'Transfer Successful';

                    $link = '&nbsp; <a href="' . route('reciept', $referenceno) . '"><i class="bi bi-download"></i>
                 Download Receipt</a>';

                    return redirect()->back()->with('success', $successMessage . ' ' . $link);
                }
            }

            // Failure logic
            $errorMessage = $response['respMsg'] ?? 'Transaction failed.';

            return redirect()->back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Error in get Account Details: ' . $e->getMessage());
            $errorMessage = 'Transaction failed.' . $response['respMsg'] ?? 'Transaction failed.';

            return redirect()->back()->with('error', $errorMessage);
        }
    }
}
