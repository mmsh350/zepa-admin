<?php

namespace App\Repositories;

use App\Models\Bonus;
use App\Models\User;
use App\Models\Wallet;

class WalletRepository
{
    public function createWalletAccount($loginUserId)
    {

        //Check if Wallet Account Existed
        $exist = User::where('id', $loginUserId)
            ->where('wallet_is_created', 0)
            ->exists();
        if ($exist) {

            //Create Wallet
            Wallet::create(
                [
                    'user_id' => $loginUserId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
            //Create Bonus Wallet
            Bonus::create(
                [
                    'user_id' => $loginUserId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
            //Update User of Wallet & Bonus Account Creation
            User::where('id', $loginUserId)->update(['wallet_is_created' => 1]);

        }

    }
}
