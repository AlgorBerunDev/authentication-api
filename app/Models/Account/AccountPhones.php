<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPhones extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'phone',
    ];
}
