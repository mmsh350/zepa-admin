<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NIN_SERVICE extends Model
{

    protected $table = 'nin_requests';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions()
    {
        return $this->belongsTo(Transaction::class, 'tnx_id');
    }
}
