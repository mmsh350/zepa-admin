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

    public function upgrade()
    {
        return $this->hasOne(Upgrade::class, 'tnx_id');
    }

    public function crmRequests()
    {
        return $this->hasOne(CRM_REQUEST::class, 'tnx_id');
    }

    public function crmRequests2()
    {
        return $this->hasOne(CRM_REQUEST2::class, 'tnx_id');
    }

    public function bvnEnrollments()
    {
        return $this->hasOne(BVNEnrollment::class, 'tnx_id');
    }


    public function bvnModifications()
    {
        return $this->hasOne(BVNModification::class, 'tnx_id');
    }

    public function acctUpgrades()
    {
        return $this->hasOne(ACC_Upgrade::class, 'tnx_id');
    }
}
