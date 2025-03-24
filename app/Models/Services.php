<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'amount',
        'category',
        'description',
        'currency',
        'service_code',
        'status'
    ];
}
