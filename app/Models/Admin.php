<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'registered_timestamp',
        'last_login_timestamp'
    ];

    protected $hidden = [
        'password',
    ];
}
