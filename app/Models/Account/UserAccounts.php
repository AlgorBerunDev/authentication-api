<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account\Accounts;
use App\Models\User;

class UserAccounts extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'account_id',
        'role'
    ];

    public function accounts() {
        return $this->belongsTo(Accounts::class, 'account_id', 'id');
    }
    public function users() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
