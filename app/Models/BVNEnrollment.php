<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BVNEnrollment extends Model
{
    use HasFactory;

    protected $table = 'bvn_enrollments';

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
