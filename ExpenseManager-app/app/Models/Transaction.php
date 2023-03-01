<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'amount',
        'account_user_id',
        'account_id'
    ];

    // public function accounts()
    // {
    //     return $this->belongsTo(Account::class);
    // }

    // public function accountUsers()
    // {   
    //     return $this->belongsTo(AccountUser::class);
    // }
}
