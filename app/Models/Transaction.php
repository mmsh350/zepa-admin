<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'referenceId',
        'service_type',
        'status',
        'type',
        'gateway',
        'service_description',
        'payerid',
        'payer_name',
        'payer_email',
        'payer_phone',
    ];

    // Define the inverse relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
