<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    const CREATED=0;
    const CANCELED=1;

    protected $fillable = [
        'user_id',
        'ip',
        'device_info',
        'status'
    ];
}
