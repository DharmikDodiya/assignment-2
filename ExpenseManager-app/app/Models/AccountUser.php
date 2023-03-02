<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'account_id'
    ];

    //AccountUser Relation To Account
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    //AccountUser Relation To Transaction
    public function transactions()
    {
        return $this->hasMany(Transaction::class,'account_user_id','id');
    }
}
