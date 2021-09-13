<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'founded_date',
        'email',
        'website',
        'amount_of_workers',
        'country',
        'city',
        'logo',
    ];
    public function users() {
        return $this->hasMany(UserAccounts::class, 'account_id', 'id');
    }
}
