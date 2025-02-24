<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    protected $fillable = ['name', 'sub_counties'];

    protected $casts = [
        'sub_counties' => 'array', // Cast the sub_counties attribute to an array
    ];
}
