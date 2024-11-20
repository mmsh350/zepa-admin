<?php

namespace App\Http\Controllers;

use App\Mail\Payment_notify_mail;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Virtual_Accounts;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MonnifyWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Verify the signature
        $signature = $request->header('Monnify-Signature');
        $computedSignature = hash_hmac('sha512', $request->getContent(), env('MONNIFYSECRET')); // hash generated
        if ($signature !== $computedSignature) {
            Log::warning('Monnify webhook signature mismatch.', ['received' => $signature, 'computed' => $computedSignature]);

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Process the webhook payload
        $payload = $request->all();
        Log::info('Monnify webhook received:', $payload);

        // Updating Payment Status)
        // Example: Update payment status
        if ($payload['eventType'] === 'SUCCESSFUL_TRANSACTION') {

            if ($payload['eventData']['paymentStatus'] == 'PAID') {
                $transactionReference = $payload['eventData']['transactionReference'];
                $amountPaid = $payload['eventData']['amountPaid'];

                $accountNumber = $payload['eventData']['destinationAccountInformation']['accountNumber'];
                $bankName = $payload['eventData']['destinationAccountInformation']['bankName'];
                $accountName = $payload['eventData']['paymentSourceInformation'][0]['accountName'];

                $response = Virtual_Accounts::select('user_id')->where('accountNo', $accountNumber)->first();
                $getUserId = $response->user_id;

                // Find and update the payment record in your database but check for duplicate first
                $exist = Transaction::where('referenceId', $transactionReference)
                    ->exists();
                if (! $exist) {
                    $user = Transaction::create([
                        'user_id' => $getUserId,
                        'payer_name' => $accountName,
                        'referenceId' => $transactionReference,
                        'service_type' => 'Wallet Topup',
                        'service_description' => 'Your wallet have been credited with ₦'.number_format($amountPaid, 2),
                        'amount' => $amountPaid,
                        'gateway' => 'Monnify',
                        'status' => 'Approved',
                    ]);

                    //Update Wallet balance
                    $wallet = Wallet::where('user_id', $getUserId)->first();

                    $balance = $wallet->balance + $amountPaid;
                    $deposit = $wallet->deposit + $amountPaid;

                    $affected = Wallet::where('user_id', $getUserId)
                        ->update(['balance' => $balance, 'deposit' => $deposit]);

                    //Send Payment Notification
                    if ($affected) {
                        //Get User Email Id
                        $getEmailId = User::select('email')->where('id', $getUserId)->first();
                        $email = $getEmailId->email;

                        //Send Mail Notification to admin and user
                        $mail_data = [
                            'type' => 'Topup',
                            'amount' => number_format($amountPaid, 2),
                            'ref' => $transactionReference,
                            'bankName' => $bankName,
                        ];
                        try {
                            //Send Notification Mail
                            $send = Mail::to($email)->send(new Payment_notify_mail($mail_data));
                        } catch (TransportExceptionInterface $e) {
                        }
                    }

                    //In App Notification
                    Notification::create([
                        'user_id' => $getUserId,
                        'message_title' => 'Top Up',
                        'messages' => 'Wallet TopUp of ₦'.number_format($amountPaid, 2).' Was Successful',
                    ]);

                }

            }
        }

        return response()->json(['status' => 'success']);
    }
}
