<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CRM_REQUEST2 extends Model
{
    use HasFactory;

    protected $table = 'crm_requests2';

    protected $fillable = [
        'user_id',
        'tnx_id',
        'refno',
        'phoneno',
        'dob',
    ];

    // Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the inverse relationship
    public function transactions()
    {
        return $this->belongsTo(Transaction::class, 'tnx_id');
    }
}
