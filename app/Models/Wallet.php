<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'deposit',
    ];

    public static function getTotalWalletBalance()
    {
        return self::sum('balance');
    }

    public static function getTotalWalletBalanceForUser()
    {
        return self::where('user_id', '469')->sum('balance');
    }
}
