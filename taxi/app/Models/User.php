<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'gender',
        'phone',
        'role',
        'is_approved',
        'county_id',    // Add this for the county
        'subcounty',    // Add this for the subcounty name
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the county associated with the driver.
     */
    public function county()
    {
        return $this->belongsTo(County::class);
    }

    //get rides for customer
    public function ridesAsCustomer()
    {
        return $this->hasMany(Ride::class, 'customer_id');
    }

    //get rides for driver
    public function ridesAsDriver()
    {
        return $this->hasMany(Ride::class, 'driver_id');
    }

    /**
     * Determine if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }
}