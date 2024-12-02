<?php

namespace App\Http\Controllers;

use App\Mail\Payment_notify_mail;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Virtual_Accounts;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MonnifyWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Verify the signature
        if (! $this->verifySignature($request)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Process the webhook payload
        $payload = $request->all();
        Log::info('Monnify webhook received:', $payload);

        switch ($payload['eventType']) {
            case 'SUCCESSFUL_TRANSACTION':
                $this->handleSuccessfulTransaction($payload);
                break;
            default:
                Log::info('Unhandled event type: ' . $payload['eventType']);
        }

        return response()->json(['status' => 'success']);
    }

    private function verifySignature(Request $request)
    {
        $signature = $request->header('Monnify-Signature');
        $computedSignature = hash_hmac('sha512', $request->getContent(), env('MONNIFYSECRET'));
        if ($signature !== $computedSignature) {
            Log::warning('Monnify webhook signature mismatch.', ['received' => $signature, 'computed' => $computedSignature]);

            return false;
        }

        return true;
    }

    private function handleSuccessfulTransaction($payload)
    {
        $eventData = $payload['eventData'];

        if ($eventData['product']['type'] === 'WEB_SDK') {
            $this->processWebSdkTransaction($eventData);
        } elseif ($eventData['product']['type'] === 'RESERVED_ACCOUNT' && $eventData['paymentStatus'] == 'PAID') {
            $this->processReservedAccountTransaction($eventData);
        }
    }

    private function processWebSdkTransaction($eventData)
    {
        $transactionReference = $eventData['transactionReference'];
        $amountPaid = $eventData['amountPaid'];
        $bankName = isset($eventData['destinationAccountInformation']['bankName'])
            ? $eventData['destinationAccountInformation']['bankName']
            : null;
        $email = $eventData['customer']['email'];

        $transaction = Transaction::where('referenceId', $transactionReference)->first();

        if ($transaction) {
            if ($transaction->status === 'Approved') {
                // Transaction is approved; no further action is needed
            } else {
                // Update the transaction based on its current status
                $status = 'Approved';
                $this->updateTransaction($transactionReference, $amountPaid, 'Monnify', 'Wallet Topup', $status);
                $this->updateWalletBalance($transaction->user_id, $amountPaid);
                $this->sendNotificationAndEmail($transaction->user_id, $amountPaid, $transactionReference, $bankName, 'Topup');
            }
        } else {
            // No existing transaction; create a new one
            $this->createNewTransaction($email, $transactionReference, $amountPaid, $bankName);
        }
    }

    private function processReservedAccountTransaction($eventData)
    {
        $transactionReference = $eventData['transactionReference'];
        $amountPaid = $eventData['amountPaid'];
        $bankName = $eventData['destinationAccountInformation']['bankName'];
        $accountNumber = $eventData['destinationAccountInformation']['accountNumber'];
        $accountName = $eventData['paymentSourceInformation'][0]['accountName'];

        $response = Virtual_Accounts::select('user_id')->where('accountNo', $accountNumber)->first();
        if ($response) {
            $this->createTransactionForReservedAccount($response->user_id, $transactionReference, $amountPaid, $bankName, $accountName);
        }
    }

    private function updateTransaction($transactionReference, $amountPaid, $gateway, $serviceType, $status)
    {
        Transaction::where('referenceId', $transactionReference)
            ->update([
                'service_type' => $serviceType,
                'service_description' => 'Your wallet has been credited with ₦' . number_format($amountPaid, 2),
                'amount' => $amountPaid,
                'gateway' => $gateway,
                'status' => $status,
            ]);
    }

    private function createNewTransaction($email, $transactionReference, $amountPaid, $bankName)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            $this->insertTransaction($user->id, $transactionReference, $amountPaid, $user->first_name . ' ' . $user->last_name, $email, $user->phone_number);
            $this->updateWalletBalance($user->id, $amountPaid);
            $this->sendNotificationAndEmail($user->id, $amountPaid, $transactionReference, $bankName, 'Topup');
        }
    }

    private function insertTransaction($userId, $transactionReference, $amountPaid, $payerName, $payerEmail, $payerPhone)
    {
        Transaction::insert([
            'user_id' => $userId,
            'payer_name' => $payerName,
            'payer_email' => $payerEmail,
            'payer_phone' => $payerPhone,
            'referenceId' => $transactionReference,
            'service_type' => 'Wallet Topup',
            'service_description' => 'Your wallet has been credited with ₦' . number_format($amountPaid, 2),
            'amount' => $amountPaid,
            'gateway' => 'Monnify',
            'status' => 'Approved',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    private function createTransactionForReservedAccount($userId, $transactionReference, $amountPaid, $bankName, $accountName)
    {
        $this->insertTransaction($userId, $transactionReference, $amountPaid, $accountName, '', '');
        $this->updateWalletBalance($userId, $amountPaid);
        $this->sendNotificationAndEmail($userId, $amountPaid, $transactionReference, $bankName, 'Topup');
    }

    private function updateWalletBalance($userId, $amountPaid)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        if ($wallet) {
            $wallet->update([
                'balance' => $wallet->balance + $amountPaid,
                'deposit' => $wallet->deposit + $amountPaid,
            ]);
        } else {
            Log::warning('Wallet not found for user ID: ' . $userId);
        }
    }

    private function sendNotificationAndEmail($userId, $amountPaid, $transactionReference, $bankName, $type)
    {
        $user = User::find($userId);
        if ($user) {
            $mail_data = [
                'type' => $type,
                'amount' => number_format($amountPaid, 2),
                'ref' => $transactionReference,
                'bankName' => $bankName,
            ];

            try {
                Mail::to($user->email)->send(new Payment_notify_mail($mail_data));
            } catch (TransportExceptionInterface $e) {
                Log::error('Error sending email for transaction ' . $transactionReference . ': ' . $e->getMessage());
            }

            Notification::create([
                'user_id' => $userId,
                'message_title' => 'Top Up',
                'messages' => 'Wallet TopUp of ₦' . number_format($amountPaid, 2) . ' was successful.',
            ]);
        }
    }
}
