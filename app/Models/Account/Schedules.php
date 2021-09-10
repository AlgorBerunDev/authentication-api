<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'monday_begin',
        'monday_end',
        'tuesday_begin',
        'tuesday_end',
        'wednesday_begin',
        'wednesday_end',
        'thursday_begin',
        'thursday_end',
        'friday_begin',
        'friday_end',
        'saturday_begin',
        'saturday_end',
        'sunday_begin',
        'sunday_end',
        'lunch_time',
    ];
}
