<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    protected $fillable = ['county_name', 'sub_counties']; // Changed from 'name' to 'county_name'

    protected $casts = [
        'sub_counties' => 'array',
    ];
}