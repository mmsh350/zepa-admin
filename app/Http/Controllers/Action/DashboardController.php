<?php

namespace App\Http\Controllers\Action;

use App\Helpers\monnifyAuthHelper;
use App\Helpers\noncestrHelper;
use App\Helpers\signatureHelper;
use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Dashboard;
use App\Models\News;
use App\Models\Notification;
use App\Models\Services;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Virtual_Accounts;
use App\Models\Wallet;
use App\Models\WalletAccount;
use App\Repositories\VirtualAccountRepository;
use App\Repositories\WalletRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function show(Request $request)
    {

        $loginUserId = Auth::id();

        Cache::remember('monnify_balance', now()->addMinutes(2), function () {
            return $this->getMonnifyBalance();
        });

        Cache::remember('palm_pay_balance', now()->addMinutes(2), function () {
            return $this->getPalmPayBalance();
        });

        $notifications = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->latest()
            ->take(3)
            ->get();

        $notifyCount = $notifications->count();

        $this->createAccounts($loginUserId);

        $walletBalance = Wallet::getTotalWalletBalance();

        $bonusBalance = Bonus::getTotalBonusBalance();

        $monnify = WalletAccount::getAccountDetailsById(1);
        $palmpay = WalletAccount::getAccountDetailsById(2);

        $transactionCount = Transaction::count();

        $userCount = User::count();

        $adminCount = User::where('role', 'admin')->count();

        $agentCount = User::where('role', 'agent')->count();

        $generalUserCount = User::where('role', 'user')->count();

        //Get Virtual Account counts
        $virtualAccountCount = Virtual_Accounts::count();

        //Get Services Count
        $servicesCount = Services::count();

        //Utility count
        $dashboard = new Dashboard;

        $totalAgencyCounts = $dashboard->getAgencyCounts();

        $totalIdentityCounts = $dashboard->getIdentityCounts();

        // Fetch News
        $newsItems = News::all();

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        // Return View with Data
        return view('dashboard', compact(
            'newsItems',
            'transactionCount',
            'userCount',
            'walletBalance',
            'bonusBalance',
            'notifications',
            'notifyCount',
            'adminCount',
            'agentCount',
            'generalUserCount',
            'virtualAccountCount',
            'servicesCount',
            'notificationsEnabled',
            'totalAgencyCounts',
            'totalIdentityCounts',
            'monnify',
            'palmpay'
        ));
    }

    // Creating Virtual Accounts
    private function createAccounts($userId)
    {
        $repObj = new VirtualAccountRepository;
        $repObj->createVirtualAccount($userId);

        $repObj2 = new WalletRepository;
        $repObj2->createWalletAccount($userId);
    }

    private function getMonnifyBalance()
    {
        try {
            $access_token = monnifyAuthHelper::auth();

            $accno = env('ACCNO');

            $handler = curl_init();

            // Set headers
            $headers = [
                'Authorization: bearer '.$access_token,
                'Content-Type: application/json',
            ];

            // Build the URL with the query parameter
            $url = env('BASE_URL')."/v2/disbursements/wallet-balance?accountNumber=$accno";

            // Set cURL options
            curl_setopt($handler, CURLOPT_URL, $url);
            curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

            // Execute the request
            $response = curl_exec($handler);

            // Check for errors
            if (curl_errno($handler)) {
                throw new Exception('Curl error: '.curl_error($handler));
            }

            // Close the handler
            curl_close($handler);

            // Decode the JSON response
            $responseData = json_decode($response, true);

            // Check if the request was successful
            if (isset($responseData['requestSuccessful']) && $responseData['requestSuccessful']) {

                // Extract the available balance
                $availableBalance = $responseData['responseBody']['availableBalance'];

                // Update the wallet account balance where id = 1
                $updated = WalletAccount::where('id', 1)
                    ->update(['available_balance' => $availableBalance]);

            if (! $updated) {
                throw new Exception('Failed to update wallet account balance.');
            }
            return  $availableBalance;

            } else {
                throw new Exception('API Error: '.($responseData['responseMessage'] ?? 'Unknown error.'));
            }

        } catch (Exception $e) {
            Log::error('Error in getMonnifyBalance: '.$e->getMessage());
        }

    }

    private function getPalmPayBalance()
    {

        try {

            $requestTime = (int) (microtime(true) * 1000);

            $noncestr = noncestrHelper::generateNonceStr();

            $data = [
                'requestTime' => $requestTime,
                'version' => env('VERSION'),
                'nonceStr' => $noncestr,
                'merchantId' => env('MERCHANTID'),
            ];

            $signature = signatureHelper::generate_signature($data, config('keys.private'));

            $url = env('BASE_URL3').'api/v2/merchant/manage/account/queryBalance';
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
                throw new Exception('cURL Error: '.curl_error($ch));
            }

            // Close cURL session
            curl_close($ch);

            // Decode the JSON response to an array
            $responseData = json_decode($response, true);

            // Check if response data is valid
            if (! isset($responseData['data']['availableBalance'])) {
                throw new Exception('Invalid response format or missing data.');
            }

            // Extract the available balance from the decoded data
            $availableBalance = $responseData['data']['availableBalance'] / 100;

            // Update the wallet account balance where id = 2
            $updated = WalletAccount::where('id', 2)
                ->update(['available_balance' => $availableBalance]);

            if (! $updated) {
                throw new Exception('Failed to update wallet account balance.');
            }
             return $availableBalance;
        } catch (Exception $e) {
            // Log the error
            Log::error('Error in getPalmPayBalance: '.$e->getMessage());
        }
    }
}
