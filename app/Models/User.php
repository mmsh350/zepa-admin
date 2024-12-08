<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'password',
        'phone_number',
        'referral_code',
        'refferral_id',
        'role',
        'is_active',
        'id_cards',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Define the relationship
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeExcludeAdmin($query)
    {
        return $query->where('id', '!=', 1); // Exclude the user with id = 1
    }

    public function upgrades()
    {
        return $this->hasMany(Upgrade::class, 'user_id');
    }

    public function crmRequests()
    {
        return $this->hasMany(CRM_REQUEST::class, 'user_id');
    }

    public function crmRequests2()
    {
        return $this->hasMany(CRM_REQUEST2::class, 'user_id');
    }

    public function bvnEnrollments()
    {
        return $this->hasMany(BVNEnrollment::class, 'user_id');
    }

     public function bvnModifications()
    {
        return $this->hasMany(BVNModification::class, 'user_id');
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
