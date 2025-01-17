<?php

namespace App\Http\Controllers\NIN;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Services;
use App\Models\Transaction;
use App\Models\Verification;
use App\Models\Wallet;
use App\Repositories\NIN_PDF_Repository;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NINController extends Controller
{

    protected $loginUserId;
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->loginUserId = Auth::id();
        $this->walletService = $walletService;
    }


    //Show NIN Page
    public function show(Request $request)
    {
        $loginUserId = $this->loginUserId; // Cache the user ID for easier readability

        // Fetch unread notifications (limit to 3 and sort by ID descending)
        $notifications = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        // Count unread notifications
        $notifyCount = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->count();

        // Fetch all required service fees in one query
        $serviceCodes = ['113', '114', '115', '116'];
        $services = Services::whereIn('service_code', $serviceCodes)->get()->keyBy('service_code');

        // Extract specific service fees
        $ServiceFee = $services->get('113');
        $regular_nin_fee = $services->get('114');
        $standard_nin_fee = $services->get('115');
        $premium_nin_fee = $services->get('116');

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        // Determine the view to return
        switch (true) {
            case $request->route()->named('nin-phone'):
                $viewName = 'nin-phone';
                break;
            case $request->route()->named('nin'):
                $viewName = 'nin-verify';
                break;
            case $request->route()->named('nin-track'):
                $viewName = 'nin-track';
                break;
            default:
                $viewName = 'nin-verify';
                break;
        }


        return view($viewName, compact(
            'notifications',
            'notifyCount',
            'ServiceFee',
            'regular_nin_fee',
            'standard_nin_fee',
            'premium_nin_fee',
            'notificationsEnabled'
        ));
    }


    public function retrieveNIN(Request $request)
    {
        // $request->validate(['nin' => 'required|numeric|digits:11']);

        if ($request->has('phone')) {

            $request->validate([
                'phone' => 'required|numeric|digits:11',
            ]);
        } elseif ($request->has('trackingId')) {
            $request->validate([
                'trackingId' => 'required|alpha_num|size:15',
            ]);
        } else {
            $request->validate([
                'nin' => 'required|numeric|digits:11',
            ]);
        }

        //NIN Services Fee
        $ServiceFee = 0;
        $ServiceFee = Services::where('service_code', '113')->first();
        $ServiceFee = $ServiceFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $ServiceFee) {
            return response()->json([
                'message' => 'Error',
                'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
            ], 422);
        } else {

            try {

                $referenceNumber = Str::upper(Str::random(10));

                if ($request->has('phone')) {
                    // Handle NIN request
                    $endpoint_part = '/nin/phone2';
                    $postdata = [
                        'value' => $request->input('phone'),
                        'ref' => $referenceNumber,
                    ];
                } elseif ($request->has('trackingId')) {
                    // Handle tracking request
                    $endpoint_part = '/nin/pulling';
                    $postdata = [
                        'trkid' => $request->input('trackingId'),
                        'ref' => $referenceNumber,
                    ];
                } else {
                    // Handle other NIN-related requests
                    $endpoint_part = '/nin/v2';
                    $postdata = [
                        'value' => $request->input('nin'),
                        'ref' => $referenceNumber,
                    ];
                }

                $endpoint = env('ENDPOINT') . $endpoint_part;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $endpoint);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    [
                        'Content-Type: application/json',
                        'Authorization: ' . env('ACCESS_TOKEN') . '',
                    ]
                );

                $response = curl_exec($ch);
                curl_close($ch);

                $data = json_decode($response, true);

                $redirect_url = "";

                if ($request->has('trackingId')) {
                     $redirect_url  = $this->handleResponse($data, $wallet, $ServiceFee, 'withTracking');
                } else {
                     $redirect_url = $this->handleResponse($data, $wallet, $ServiceFee);
                }

                if( $redirect_url == 1){

                      return json_encode(['status' => $data['success'], 'data' => $data]);
                }
                else  if( $redirect_url == 2){
                   return response()->json(['status' => 'Not Found','errors' => ['Succesfully Verified with ' . $data['data']['reason']],], 422);

                }
                else
                {
                    return response()->json(['status' => 'Not Found','errors' => ['Verification Failed !'],], 422);

                }


                //$data = $this->formatAndDecodeJson($response);

            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'Request failed',
                    'errors' => ['An error occurred while making the API request'],
                ], 422);
            }
        }
    }

    public function regularSlip($nin_no)
    {
        //NIN Services Fee
        $ServiceFee = 0;
        $ServiceFee = Services::where('service_code', '114')->first();
        $ServiceFee = $ServiceFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $ServiceFee) {
            return response()->json([
                'message' => 'Error',
                'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
            ], 422);
        } else {
            $balance = $wallet->balance - $ServiceFee;

            $affected = Wallet::where('user_id', $this->loginUserId)
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

            $user = Transaction::create([
                'user_id' => $this->loginUserId,
                'payer_name' => $payer_name,
                'payer_email' => $payer_email,
                'payer_phone' => $payer_phone,
                'referenceId' => $referenceno,
                'service_type' => 'Regular NIN Slip',
                'service_description' => 'Wallet debitted with a service fee of ₦' . number_format($ServiceFee, 2),
                'amount' => $ServiceFee,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);

            $this->walletService->creditDeveloperWallet($payer_name, $payer_email, $payer_phone, $referenceno . "C2w", "slip_download");

            //Generate PDF
            $repObj = new NIN_PDF_Repository;
            $response = $repObj->regularPDF($nin_no);

            return $response;
        }
    }

    public function standardSlip($nin_no)
    {

        //NIN Services Fee
        $ServiceFee = 0;
        $ServiceFee = Services::where('service_code', '115')->first();
        $ServiceFee = $ServiceFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $ServiceFee) {
            return response()->json([
                'message' => 'Error',
                'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
            ], 422);
        } else {
            $balance = $wallet->balance - $ServiceFee;

            $affected = Wallet::where('user_id', $this->loginUserId)
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

            $user = Transaction::create([
                'user_id' => $this->loginUserId,
                'payer_name' => $payer_name,
                'payer_email' => $payer_email,
                'payer_phone' => $payer_phone,
                'referenceId' => $referenceno,
                'service_type' => 'Standard NIN Slip',
                'service_description' => 'Wallet debitted with a service fee of ₦' . number_format($ServiceFee, 2),
                'amount' => $ServiceFee,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);

            $this->walletService->creditDeveloperWallet($payer_name, $payer_email, $payer_phone, $referenceno . "C2w", "slip_download");

            //Generate PDF
            $repObj = new NIN_PDF_Repository;
            $response = $repObj->standardPDF($nin_no);

            return $response;
        }
    }

    public function premiumSlip($nin_no)
    {
        //NIN Services Fee
        $ServiceFee = 0;
        $ServiceFee = Services::where('service_code', '116')->first();
        $ServiceFee = $ServiceFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $ServiceFee) {
            return response()->json([
                'message' => 'Error',
                'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
            ], 422);
        } else {
            $balance = $wallet->balance - $ServiceFee;

            $affected = Wallet::where('user_id', $this->loginUserId)
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

            $user = Transaction::create([
                'user_id' => $this->loginUserId,
                'payer_name' => $payer_name,
                'payer_email' => $payer_email,
                'payer_phone' => $payer_phone,
                'referenceId' => $referenceno,
                'service_type' => 'Premium NIN Slip',
                'service_description' => 'Wallet debitted with a service fee of ₦' . number_format($ServiceFee, 2),
                'amount' => $ServiceFee,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);

            $this->walletService->creditDeveloperWallet($payer_name, $payer_email, $payer_phone, $referenceno . "C2w", "slip_download");

            //Generate PDF
            $repObj = new NIN_PDF_Repository;
            $response = $repObj->premiumPDF($nin_no);

            return $response;
        }
    }

    private function formatAndDecodeJson($jsonString)
    {

        $replaceString = '||||statusCode||||200||||data||||message||||90';
        $replaceString2 = '[]}||||21||||';

        //Replace Json
        $cleanedString = str_replace($replaceString, '', $jsonString);
        $cleanedString = str_replace($replaceString2, '', $cleanedString);

        // Remove newline characters and excessive whitespace
        $formattedString = preg_replace('/\s+/', ' ', $cleanedString);

        // Fix potential issues with escaped quotes
        $formattedString = str_replace('\"', '"', $formattedString);

        // Trim leading and trailing whitespace
        $formattedString = trim($formattedString) . '}';

        //return $formattedString;

        // Decode the JSON string
        $jsonData = json_decode($formattedString, true);

        return $jsonData;
    }

    private function processResponseData($data, $type)
    {

        if ($type == 'withTracking') {


                $user = Verification::create([
                'idno' => $data['data']['nin'],
                'type' => 'NIN',
                'nin' => $data['data']['nin'],
                'trackingId' => $data['data']['trackingId'],
                'first_name' => $data['data']['firstname'],
                'middle_name' => $data['data']['middlename'],
                'last_name' => $data['data']['lastname'],
                // 'phoneno' => $data['data']['mobile'],
                // 'email' => $data['data']['email'],
                'dob' => '1970-01-01',
                'gender' => $data['data']['gender'] == 'm' || $data['data']['gender'] == 'Male' ? 'Male' : 'Female',
                'state' => $data['data']['state'],
                'lga' => $data['data']['residence_lga'],
                'address' => $data['data']['address'],
                'photo' => $data['data']['face'],
            ]);


        } else {



        $user = Verification::create([
            'idno' => $data['data']['idNumber'],
            'type' => 'NIN',
            'nin' => $data['data']['idNumber'],
            // 'trackingId' => $data['nin_data']['trackingId'],
            // 'title' => $data['nin_data']['title'],
            'first_name' => $data['data']['firstName'],
            'middle_name' => $data['data']['middleName'],
            'last_name' => $data['data']['lastName'],
            'phoneno' => $data['data']['mobile'],
            'email' => $data['data']['email'],
            'dob' => $data['data']['dateOfBirth'],
            'gender' => $data['data']['gender'] == 'm' || $data['data']['gender'] == 'Male' ? 'Male' : 'Female',
            'state' => $data['data']['state'],
            'lga' => $data['data']['lga'],
            'address' => $data['data']['addressLine'],
            'photo' => $data['data']['image'],
        ]);
        }
    }

    private function handleResponse($data, $wallet, $ServiceFee, $type = "Normal")
    {

        if ($data['success'] == true && ($data['data']['status'] == 'found' || $data['data']['status'] == 1)) {


            $this->processResponseData($data, $type);

            $balance = $wallet->balance - $ServiceFee;

            $affected = Wallet::where('user_id', $this->loginUserId)
                ->update(['balance' => $balance]);

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
                'service_type' => 'NIN Verification',
                'service_description' => 'Wallet debitted with a service fee of ₦' . number_format($ServiceFee, 2),
                'amount' => $ServiceFee,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);

            //Return Json response
            return 1;
        }else if($data['success'] == true && ($data['data']['status'] == 'not_found' || $data['data']['status'] == 0)){

            $this->processResponseData($data, $type);

            $balance = $wallet->balance - $ServiceFee;

            $affected = Wallet::where('user_id', $this->loginUserId)
                ->update(['balance' => $balance]);

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
                'service_type' => 'NIN Verification',
                'service_description' => 'Wallet debitted with a service fee of ₦' . number_format($ServiceFee, 2),
                'amount' => $ServiceFee,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);
            return 2;
        }else {
            return 0;
        }
    }
}
