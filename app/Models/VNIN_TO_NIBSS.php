<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VNIN_TO_NIBSS extends Model
{
    protected $table = 'vnin_to_nibss';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions()
    {
        return $this->belongsTo(Transaction::class, 'tnx_id');
    }
}
