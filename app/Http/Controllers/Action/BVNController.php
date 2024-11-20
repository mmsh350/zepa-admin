<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Services;
use App\Models\Transaction;
use App\Models\Verification;
use App\Models\Wallet;
use App\Traits\ActiveUsers;
use App\Traits\KycVerify;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BVNController extends Controller
{
    use ActiveUsers;
    use KycVerify;

    //Show BVN Page
    public function show(Request $request)
    {
        //Login User Id
        $loginUserId = Auth::id();

        //Check if user is Disabled
        if ($this->is_active() != 1) {
            Auth::logout();

            return view('error');
        }

        //Check if user is Pending, Rejected, or Verified KYC
        $status = $this->is_verified();

        if ($status == 'Pending') {
            return redirect()->route('verification.kyc');

        } elseif ($status == 'Submitted') {
            return view('kyc-status')->with(compact('status'));

        } elseif ($status == 'Rejected') {
            return view('kyc-status')->with(compact('status'));
        } else {

            //Notification Data
            $notifications = Notification::all()->where('user_id', $loginUserId)
                ->sortByDesc('id')
                ->where('status', 'unread')
                ->take(3);

            //Notification Count
            $notifycount = 0;
            $notifycount = Notification::all()
                ->where('user_id', $loginUserId)
                ->where('status', 'unread')
                ->count();

            //BVN Verification Services Fee
            $BVNFee = 0;
            $BVNFee = Services::where('service_code', '101')->first();

            //BVN Standard Services Fee
            $bvn_standard_fee = 0;
            $bvn_standard_fee = Services::where('service_code', '102')->first();

            //BVN Premium Services Fee
            $bvn_premium_fee = 0;
            $bvn_premium_fee = Services::where('service_code', '103')->first();

            return view('bvn-verify')
                ->with(compact('notifications'))
                ->with(compact('BVNFee'))
                ->with(compact('bvn_premium_fee'))
                ->with(compact('bvn_standard_fee'))
                ->with(compact('notifycount'));
        }
    }

    public function retrieveBVN(Request $request)
    {

        $request->validate(['bvn' => 'required|numeric|digits:11']);

        $loginUserId = Auth::id();

        //BVN Services Fee
        $BVNFee = 0;
        $BVNFee = Services::where('service_code', '101')->first();
        $BVNFee = $BVNFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $BVNFee) {
            return response()->json([
                'message' => 'Error',
                'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
            ], 422);
        } else {

            try {

                $client = new \GuzzleHttp\Client;

                $response = $client->request('POST', 'https://api.prembly.com/identitypass/verification/bvn', [
                    'form_params' => [
                        'number' => $request->bvn,
                    ],
                    'headers' => [
                        'accept' => 'application/json',
                        'app-id' => env('appId'),
                        'content-type' => 'application/x-www-form-urlencoded',
                        'x-api-key' => env('xApiKey'),
                    ],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                if ($data['status'] == true) {

                    //Update DB with Verification Details
                    $user = Verification::create(
                        [
                            'idno' => $data['data']['bvn'] ? $data['data']['bvn'] : $data['data']['number'],
                            'type' => 'BVN',
                            'nin' => $data['data']['nin'],
                            'first_name' => $data['data']['firstName'],
                            'middle_name' => $data['data']['middleName'],
                            'last_name' => $data['data']['lastName'],
                            'phoneno' => $data['data']['phoneNumber1'],
                            'email' => $data['data']['email'],
                            'dob' => $data['data']['dateOfBirth'],
                            'gender' => $data['data']['gender'],
                            'enrollment_branch' => $data['data']['enrollmentBranch'],
                            'enrollment_bank' => $data['data']['enrollmentBank'],
                            'registration_date' => $data['data']['registrationDate'],
                            'address' => $data['data']['residentialAddress'],
                            'photo' => $data['data']['base64Image'],
                        ]
                    );

                    $balance = $wallet->balance - $BVNFee;

                    $affected = Wallet::where('user_id', $loginUserId)
                        ->update(['balance' => $balance]);

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
                        'user_id' => $loginUserId,
                        'payer_name' => $payer_name,
                        'payer_email' => $payer_email,
                        'payer_phone' => $payer_phone,
                        'referenceId' => $referenceno,
                        'service_type' => 'BVN Verification',
                        'service_description' => 'Wallet debitted with a service fee of ₦'.number_format($BVNFee, 2),
                        'amount' => $BVNFee,
                        'gateway' => 'Wallet',
                        'status' => 'Approved',
                    ]);

                    //Return Json response
                    return json_encode(['status' => $data['status'], 'data' => $data]);

                } elseif ($data['status'] == false) {
                    $errMsg = '';
                    if (isset($data['message'])) {
                        $errMsg = $data['message'];
                    }

                    return response()->json([
                        'status' => 'Request failed',
                        'errors' => ['Request failed please try again later. '.$errMsg],
                    ], 422);

                }

            } catch (RequestException $e) {
                return response()->json([
                    'status' => 'Request failed',
                    'errors' => ['Request failed cannot connect to server, please try again later. '],
                ], 422);
            }

        }

    }

    public function premiumBVN($bvnno)
    {
        $loginUserId = Auth::id();

        //BVN Services Fee
        $BVNFee = 0;
        $BVNFee = Services::where('service_code', '103')->first();
        $BVNFee = $BVNFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $BVNFee) {
            return response()->json([
                'message' => 'Error',
                'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
            ], 422);
        } else {
            $balance = $wallet->balance - $BVNFee;

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

            $user = Transaction::create([
                'user_id' => $loginUserId,
                'payer_name' => $payer_name,
                'payer_email' => $payer_email,
                'payer_phone' => $payer_phone,
                'referenceId' => $referenceno,
                'service_type' => 'Premium BVN Slip',
                'service_description' => 'Wallet debitted with a service fee of ₦'.number_format($BVNFee, 2),
                'amount' => $BVNFee,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);

            if (Verification::where('idno', $bvnno)->exists()) {

                $veridiedRecord = Verification::where('idno', $bvnno)
                    ->latest()
                    ->first();

                $data = $veridiedRecord;
                $view = view('PremiumBVN', compact('veridiedRecord'))->render();

                return response()->json(['view' => $view]);

            } else {

                return response()->json([
                    'message' => 'Error',
                    'errors' => ['Not Found' => 'Verification record not found !'],
                ], 422);

            }

        }

    }

    public function standardBVN($bvnno)
    {
        $loginUserId = Auth::id();

        //BVN Services Fee
        $BVNFee = 0;
        $BVNFee = Services::where('service_code', '102')->first();
        $BVNFee = $BVNFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $BVNFee) {
            return response()->json([
                'message' => 'Error',
                'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
            ], 422);
        } else {
            $balance = $wallet->balance - $BVNFee;

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

            $user = Transaction::create([
                'user_id' => $loginUserId,
                'payer_name' => $payer_name,
                'payer_email' => $payer_email,
                'payer_phone' => $payer_phone,
                'referenceId' => $referenceno,
                'service_type' => 'Standard BVN Slip',
                'service_description' => 'Wallet debitted with a service fee of ₦'.number_format($BVNFee, 2),
                'amount' => $BVNFee,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);

            if (Verification::where('idno', $bvnno)->exists()) {

                $veridiedRecord = Verification::where('idno', $bvnno)
                    ->latest()
                    ->first();

                $data = $veridiedRecord;
                $view = view('freeBVN', compact('veridiedRecord'))->render();

                return response()->json(['view' => $view]);

            } else {

                return response()->json([
                    'message' => 'Error',
                    'errors' => ['Not Found' => 'Verification record not found !'],
                ], 422);

            }

        }
    }
}
