<?php

namespace App\Repositories;

use App\Helpers\noncestrHelper;
use App\Helpers\signatureHelper;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VirtualAccountRepository
{
    public function createVirtualAccount($loginUserId)
    {

        $exist = User::where('id', $loginUserId)
            ->where('vwallet_is_created', 0)
            ->exists();
        if ($exist) {

            $this->verifyBVN($loginUserId);

            $customer_name = trim(auth()->user()->first_name . ' ' . auth()->user()->middle_name . ' ' . auth()->user()->last_name);

            try {

                $requestTime = (int) (microtime(true) * 1000);
                $noncestr = noncestrHelper::generateNonceStr();
                $accountReference = "ZW" . strtoupper(bin2hex(random_bytes(5)));

                $data = [
                    'requestTime' => $requestTime,
                    'identityType' => 'personal',
                    'licenseNumber' =>  auth()->user()->idNumber,
                    'virtualAccountName' => $customer_name,
                    'version' => env('VERSION'),
                    'customerName' => $customer_name,
                    'email' => auth()->user()->email,
                    'accountReference' => $accountReference,
                    'nonceStr' => $noncestr,
                ];

                $signature = signatureHelper::generate_signature($data, config('keys.private'));

                $url = env('BASE_URL3') . 'api/v2/virtual/account/label/create';
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
                    throw new Exception('Request was not successful.');
                }

                // Check for success
                if (isset($response['respCode']) && $response['respCode'] === '00000000') {

                    $res =  DB::table('virtual_accounts')->insert([
                        'user_id' => $loginUserId,
                        'accountReference' => $response['data']['accountReference'],
                        'accountNo' => $response['data']['virtualAccountNo'],
                        'accountName' => $response['data']['virtualAccountName'],
                        'bankName' => 'PalmPay',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Update user to indicate virtual account creation
                    User::where('id', $loginUserId)->update(['vwallet_is_created' => 1]);
                }
            } catch (\Exception $e) {
                Log::error('Error creating virtual account for user ' . $loginUserId . ': ' . $e->getMessage());

                return response()->json(['error' => 'Failed to create virtual account.'], 500);
            }
        }
    }

    private function verifyBVN($loginUserId)
    {
        try {

            $bvn_no = auth()->user()->idNumber;

            $requestTime = (int) (microtime(true) * 1000);

            $noncestr = noncestrHelper::generateNonceStr();

            $data = [

                'version' => env('VERSION'),
                'nonceStr' => $noncestr,
                'requestTime' => $requestTime,
                'bvn' => $bvn_no,
            ];

            $signature = signatureHelper::generate_signature($data, config('keys.private2'));

            $url = env('Domain') . '/api/validator-service/open/bvn/inquire';
            $token = env('BEARER');
            $headers = [
                'Accept: application/json, text/plain, */*',
                'CountryCode: NG',
                "Signature: $signature",
                'Content-Type: application/json',
                "Authorization: Bearer $token",
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


            $data = json_decode($response, true);

            if ($data['respCode'] == 00000000) {

                $data = $data['data'];

                $updateData = [
                    'first_name'   => ucwords(strtolower($data['firstName'])),
                    'middle_name'  => ucwords(strtolower($data['middleName'])) ?? null,
                    'last_name'    => ucwords(strtolower($data['lastName'])),
                    'dob'          => $data['birthday'],
                    'gender'       => $data['gender'],
                ];

                if (!empty($data['phoneNumber'])) {
                    $updateData['phone_number'] = $data['phoneNumber'];
                }

                if (!empty($data['photo'])) {
                    $updateData['profile_pic'] = $data['photo'];
                }
                User::where('id', $loginUserId)->update($updateData);
            } else if ($data['respCode'] == 99120020 || $data['respCode'] == 99120024) {

                return redirect()->back()->with('error', 'Invalid or suspended BVN detected. Please update your BVN information and try again.');
            } else {
                return redirect()->back()->with('error', 'An error occurred while making the BVN Verification (System Err)');
            }
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'An error occurred while making the BVN Verification');
        }
    }
}
