<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    /**
     * Get counts of CRMRequest and CRMRequest2.
     *
     * @return array
     */
    public function getAgencyCounts(): int
    {
        return
            CRM_REQUEST::count() +
            CRM_REQUEST2::count() +
            BVNModification::count() +
            BVNEnrollment::count() +
            ACC_Upgrade::count();
    }

    public function getIdentityCounts()
    {
        return Verification::count();
    }
}
