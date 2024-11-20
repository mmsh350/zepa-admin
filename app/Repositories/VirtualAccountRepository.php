<?php

namespace App\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VirtualAccountRepository
{
    public function createVirtualAccount($loginUserId)
    {

        //Retrieve the Bank Code
        $bankCode1 = env('BANKCODE1');
        $bankCode2 = env('BANKCODE2');

        //Check if Virtual Account Existed
        $exist = User::where('id', $loginUserId)
            ->where('vwallet_is_created', 0)
            ->exists();
        if ($exist) {

            $AccessKey = env('MONNIFYAPI').':'.env('MONNIFYSECRET');
            $ApiKey = base64_encode($AccessKey);

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => env('BASE_URL').'/v1/auth/login/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => [
                    "Authorization: Basic {$ApiKey}",
                ],
            ]);

            $json = curl_exec($ch);
            $result = json_decode($json);
            curl_close($ch);

            //Retrieve accessToken from response body
            $accessToken = $result->responseBody->accessToken;
            $random = md5(uniqid(Auth::user()->email));
            $refno = substr(strtolower($random), 0, 11);
            $bvn = Auth::user()->idNumber;
            //Request Account Creation
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => env('BASE_URL2').'/v2/bank-transfer/reserved-accounts',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
											"accountReference": "'.$refno.'",
											"accountName": "'.Auth::user()->first_name.'",
											"currencyCode": "NGN",
											"contractCode": "'.env('MONNIFYCONTRACT').'",
											"customerEmail": "'.Auth::user()->email.'",
											"customerName": "'.Auth::user()->first_name.' '.Auth::user()->last_name.'",
											"bvn":"'.$bvn.'",
                                            "getAllAvailableBanks": false,
											"preferredBanks": ["'.$bankCode1.'","'.$bankCode2.'"]
									 }',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer '.$accessToken,
                    'Content-Type: application/json',
                ],
            ]);

            $response = curl_exec($curl);
            curl_close($curl);
            $retrieveData = json_decode($response, true);

            //Get the response and store into db
            try {
                // Proceed only if the request was successful
                if (! $retrieveData['requestSuccessful']) {
                    throw new Exception('Request was not successful.');
                }

                $responseBody = $retrieveData['responseBody'];
                $account_name = 'Fee24 consultant LTD-'.$responseBody['accountName'];
                $accountReference = $responseBody['accountReference'];
                $accounts = $responseBody['accounts'];

                $insertData = [];

                // Iterate through accounts and prepare data for insertion
                foreach ($accounts as $account) {
                    if (in_array($account['bankCode'], [$bankCode1, $bankCode2])) {
                        $insertData[] = [
                            'user_id' => $loginUserId,
                            'accountReference' => $accountReference,
                            'accountNo' => $account['accountNumber'],
                            'accountName' => $account_name,
                            'bankName' => $account['bankName'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // Perform batch insert if there is data to insert
                if (! empty($insertData)) {
                    DB::table('virtual_accounts')->insert($insertData);
                }

                // Update user to indicate virtual account creation
                User::where('id', $loginUserId)->update(['vwallet_is_created' => 1]);

            } catch (\Exception $e) {
                // Log the exception with a unique identifier
                Log::error('Error creating virtual account for user '.$loginUserId.': '.$e->getMessage());

                // Return a JSON response with error information
                return response()->json(['error' => 'Failed to create virtual account.'], 500);
            }

        }

    }
}
