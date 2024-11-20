<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Transaction;
use Illuminate\Support\Str;

class TransactionService
{
    public function createTransaction($transactionData)
    {
        // Generate reference number securely
        $referenceno = Str::random(12);

        // Create transaction record
        Transaction::create([
            'user_id' => $transactionData['user_id'],
            'payer_name' => $transactionData['payer_name'],
            'payer_email' => $transactionData['payer_email'],
            'payer_phone' => $transactionData['payer_phone'],
            'referenceId' => $referenceno,
            'service_type' => $transactionData['service_type'],
            'service_description' => $transactionData['service_description'],
            'amount' => $transactionData['amount'],
            'gateway' => $transactionData['gateWay'],
            'status' => $transactionData['status'],
        ]);

    }

    public function createNotification($userId, $messageTitle, $message)
    {
        // Create notification record
        Notification::create([
            'user_id' => $userId,
            'message_title' => $messageTitle,
            'messages' => $message,
        ]);
    }
}
