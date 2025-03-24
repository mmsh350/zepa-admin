<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    protected $table = 'bonus_histories';

    protected $fillable = [
        'user_id',
        'balance',
        'deposite',
    ];

    public static function getTotalBonusBalance()
    {
        return self::sum('amount');
    }
}
