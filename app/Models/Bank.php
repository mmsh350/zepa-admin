<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks';

    // Define the fillable fields
    protected $fillable = [
        'bank_code',
        'bank_name',
        'bank_url',
        'bg_url',
    ];
}
