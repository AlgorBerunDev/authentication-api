<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    use HasFactory;
    const CREATED=0;
    const CANCELED=1;
    const EXPIRED=2;
    const BLOCKED=3;
    const USER_BLOCKED=4;
    const VERIFIED=5;

    protected $fillable = [
        'user_id',
        'session_id',
        'code',
    ];
}
