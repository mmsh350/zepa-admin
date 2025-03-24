<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceStatus extends Model
{
    public function scopeExcludeAdminPayout($query)
    {
        return $query->where('service_id', '!=', '1');
    }
}
