<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletAccount extends Model
{
    protected $table = 'wallet_account_balance';

    protected $fillable = [
        'available_balance',
    ];

    public static function getAccountDetailsById(int $id)
    {

        $account = self::select('account_name', 'available_balance')
            ->where('id', $id)
            ->first();

        return $account ? [
            'accountName' => $account->account_name,
            'availableBalance' => $account->available_balance,
        ] : null;
    }
}
