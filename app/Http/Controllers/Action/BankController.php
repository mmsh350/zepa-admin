<?php

namespace App\Http\Controllers\Action;

use App\Helpers\noncestrHelper;
use App\Helpers\signatureHelper;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Services;
use App\Models\Transaction;
use App\Models\Wallet;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{
    protected $loginUserId;

    // Constructor to initialize the property
    public function __construct()
    {
        $this->loginUserId = Auth::id();
    }

    //Show BVN Page
    public function show(Request $request) {}

    public function retrieveBank(Request $request)
    {

        $request->validate([
            'accountNumber' => 'required|numeric|digits:10',
            'bankcode' => ['required', 'string'],
        ]);

        //Bank Services Fee
        $ServicesFee = 0;
        $ServicesFee = Services::where('service_code', '104')->first();
        $ServicesFee = $ServicesFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $ServicesFee) {
            return response()->json([
                'message' => 'Error',
                'errors' => ['Wallet Error' => 'Sorry Wallet Not Sufficient for Transaction !'],
            ], 422);
        } else {

            try {

                $client = new \GuzzleHttp\Client;

                $response = $client->request('POST', 'https://api.prembly.com/identitypass/verification/bank_account/advance', [
                    'form_params' => [
                        'number' => $request->accountNumber,
                        'bank_code' => $request->bankcode,
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

                    $balance = $wallet->balance - $ServicesFee;

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

                    $payer_name = auth()->user()->first_name.' '.Auth::user()->last_name;
                    $payer_email = auth()->user()->email;
                    $payer_phone = auth()->user()->phone_number;

                    Transaction::create([
                        'user_id' => $this->loginUserId,
                        'payer_name' => $payer_name,
                        'payer_email' => $payer_email,
                        'payer_phone' => $payer_phone,
                        'referenceId' => $referenceno,
                        'service_type' => 'Bank Account Verification',
                        'service_description' => 'Wallet debitted with a service fee of ₦'.number_format($ServicesFee, 2),
                        'amount' => $ServicesFee,
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

    public function genBankCodes()
    {
        try {
            $requestTime = (int) (microtime(true) * 1000);

            $noncestr = noncestrHelper::generateNonceStr();

            $data = [
                'requestTime' => $requestTime,
                'version' => env('VERSION'),
                'nonceStr' => $noncestr,
                'merchantId' => env('MERCHANTID'),
                'businessType' => 0,
            ];

            $signature = signatureHelper::generate_signature($data, config('keys.private'));

            $url = env('BASE_URL3').'api/v2/general/merchant/queryBankList';
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
                throw new \Exception('cURL Error: '.curl_error($ch));
            }

            // Close cURL session
            curl_close($ch);

            // Decode the JSON response to an associative array
            $responseData = json_decode($response, true);

            // Handle cases where the response is invalid
            if (! is_array($responseData)) {
                throw new \Exception('Invalid JSON response from API');
            }

            // Check if the 'data' key exists and it's an array
            if (isset($responseData['data']) && is_array($responseData['data'])) {
                // Loop through each bank in the data array
                foreach ($responseData['data'] as $bank) {
                    // Ensure that 'bankCode' and other keys exist before using them
                    if (isset($bank['bankCode'], $bank['bankName'], $bank['bankUrl'], $bank['bgUrl'])) {
                        // Call the function to create or update the bank record
                        $this->createOrUpdateBank([
                            'bankCode' => $bank['bankCode'],
                            'bankName' => $bank['bankName'],
                            'bankUrl' => $bank['bankUrl'],
                            'bgUrl' => $bank['bgUrl'],
                        ]);
                    } else {
                        // Handle the case where any expected key is missing
                        throw new \Exception('Missing data for bank: '.json_encode($bank));
                    }
                }
            } else {
                throw new \Exception('No data key or invalid format in response');
            }

            return response()->json(200);

        } catch (\Exception $e) {
            Log::error('Error in genBankCodes: '.$e->getMessage());
        }
    }

    public function createOrUpdateBank(array $data)
    {
        // Check if a bank with the given bankCode already exists
        $bank = Bank::updateOrCreate(
            ['bank_code' => $data['bankCode']], // Search condition
            [   // Data to update or insert
                'bank_name' => $data['bankName'],
                'bank_url' => $data['bankUrl'],
                'bg_url' => $data['bgUrl'],
            ]
        );
    }

    public function fetchBanks()
    {
        // Fetch bank codes from the database
        $banks = DB::table('bank_codes')->select(['name', 'code'])->get();

        // Return the bank codes as a JSON response
        return response()->json($banks);
    }

    public function getBankAccount()
    {

        try {
            $requestTime = (int) (microtime(true) * 1000);

            $noncestr = noncestrHelper::generateNonceStr();

            $data = [
                'requestTime' => $requestTime,
                'version' => env('VERSION'),
                'nonceStr' => $noncestr,
                'bankCode' => '100004',
                'bankAccNo' => '023408103440497',
            ];

            $signature = signatureHelper::generate_signature($data, config('keys.private'));

            $url = env('BASE_URL3').'api/v2/payment/merchant/payout/queryBankAccount';
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
                throw new \Exception('cURL Error: '.curl_error($ch));
            }

            // Close cURL session
            curl_close($ch);

            // Decode the JSON response to an associative array
            $responseData = json_decode($response, true);
            echo $response;

        } catch (\Exception $e) {
            Log::error('Error in get Account Details: '.$e->getMessage());

        }

    }
}
