<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'account_number',
        'user_id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    // public function accountUsers()
    // {
    //     return $this->hasMany(AccountUser::class);
    // }

    // public function transactions()
    // {
    //     return $this->hasMany(Transaction::class);
    // }
}
