<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Services;
use App\Models\Transaction;
use App\Models\Verification;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BVNController extends Controller
{

    protected $loginUserId;

    // Constructor to initialize the property
    public function __construct()
    {
        $this->loginUserId = Auth::id();
    }
    // Show BVN Page
    public function show(Request $request)
    {

        // Fetch unread notifications (limit to 3, sorted by ID descending)
        $notifications = Notification::where('user_id', $this->loginUserId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        // Count unread notifications
        $notifyCount = Notification::where('user_id', $this->loginUserId)
            ->where('status', 'unread')
            ->count();

        // Fetch BVN service fees
        $serviceCodes = ['101', '102', '103'];
        $services = Services::whereIn('service_code', $serviceCodes)
            ->get()
            ->keyBy('service_code');

        $BVNFee = $services->get('101');
        $bvn_standard_fee = $services->get('102');
        $bvn_premium_fee = $services->get('103');

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        // Return the view with all necessary data
        return view('bvn-verify', compact(
            'notifications',
            'notifyCount',
            'BVNFee',
            'bvn_standard_fee',
            'bvn_premium_fee',
            'notificationsEnabled'
        ));
    }


    public function retrieveBVN(Request $request)
    {

        $request->validate(['bvn' => 'required|numeric|digits:11']);

        //BVN Services Fee
        $BVNFee = 0;
        $BVNFee = Services::where('service_code', '101')->first();
        $BVNFee = $BVNFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $BVNFee) {
            return response()->json([
                'message' => 'Error',
                'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
            ], 422);
        } else {

            try {

                $referenceNumber = random_int(1000000000, 9999999999);
                $postdata = [
                    'value' => $request->input('bvn'),
                    'ref' => $referenceNumber,
                ];

                $endpoint_part = '/bvn2/verify';
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

                $data = $this->formatAndDecodeJson($response);

                if ($data['success'] == true && $data['data']['status'] == 'found') {

                    $this->processResponseData($data);

                    $balance = $wallet->balance - $BVNFee;

                    $affected = Wallet::where('user_id', $this->loginUserId)
                        ->update(['balance' => $balance]);

                    $referenceno = '';
                    srand((float) microtime() * 1000000);
                    $gen = '123456123456789071234567890890';
                    $gen .= 'aBCdefghijklmn123opq45rs67tuv89wxyz';
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
                        'service_type' => 'BVN Verification',
                        'service_description' => 'Wallet debitted with a service fee of ₦' . number_format($BVNFee, 2),
                        'amount' => $BVNFee,
                        'gateway' => 'Wallet',
                        'status' => 'Approved',
                    ]);

                    //Return Json response
                    return json_encode(['status' => $data['success'], 'data' => $data]);
                } else {
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
                        'service_description' => 'Wallet debitted with a service fee of ₦' . number_format(
                            $BVNFee,
                            2
                        ),
                        'amount' => $BVNFee,
                        'gateway' => 'Wallet',
                        'status' => 'Approved',
                    ]);

                    return response()->json([
                        'status' => 'Not Found',
                        'errors' => ['Succesfully Verified with ' . $data['data']['reason']],
                    ], 422);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'Request failed',
                    'errors' => ['An error occurred while making the API request'],
                ], 422);
            }
        }
    }

    public function premiumBVN($bvnno)
    {
        //BVN Services Fee
        $BVNFee = 0;
        $BVNFee = Services::where('service_code', '103')->first();
        $BVNFee = $BVNFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $BVNFee) {
            return response()->json([
                'message' => 'Error',
                'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
            ], 422);
        } else {
            $balance = $wallet->balance - $BVNFee;

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
                'service_type' => 'Premium BVN Slip',
                'service_description' => 'Wallet debitted with a service fee of ₦' . number_format($BVNFee, 2),
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
        //BVN Services Fee
        $BVNFee = 0;
        $BVNFee = Services::where('service_code', '102')->first();
        $BVNFee = $BVNFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $BVNFee) {
            return response()->json([
                'message' => 'Error',
                'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
            ], 422);
        } else {
            $balance = $wallet->balance - $BVNFee;

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
                'service_type' => 'Standard BVN Slip',
                'service_description' => 'Wallet debitted with a service fee of ₦' . number_format($BVNFee, 2),
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
    private function processResponseData($data)
    {

        // //Update DB with Verification Details
        $user = Verification::create(
            [
                'idno' => $data['data']['idNumber'],
                'type' => 'BVN',
                'nin' => $data['data']['nin'],
                'first_name' => $data['data']['firstName'],
                'middle_name' => $data['data']['middleName'],
                'last_name' => $data['data']['lastName'],
                'phoneno' => $data['data']['mobile'],
                // 'email' => $data['data']['email'],
                'dob' => $data['data']['dateOfBirth'],
                'gender' => $data['data']['gender'],
                'enrollment_branch' => $data['data']['enrollmentBranch'],
                'enrollment_bank' => $data['data']['enrollmentInstitution'],
                // 'registration_date' => '',
                // 'address' =>'',
                'photo' => $data['data']['image'],
            ]
        );
    }

    private function formatAndDecodeJson($jsonString)
    {
        $replaceString = '||||statusCode||||200||||data||||message||||0';
        $replaceString2 = '[]}||||35||||';

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

    public function formatDate($date)
    {
        // Check if date is already in the format 'Y-m-d'
        if (Carbon::hasFormat($date, 'Y-m-d')) {
            return $date;
        }

        // Check and convert if date is in 'd-M-Y' format
        try {
            return Carbon::createFromFormat('d-M-Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            // Handle invalid date format if necessary
            return null;
        }
    }
}
