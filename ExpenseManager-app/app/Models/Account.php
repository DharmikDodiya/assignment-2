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

    //Account Relation to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Account Relation To User
    public function accountUsers()
    {
        return $this->hasMany(AccountUser::class,'account_id','id');
    }

    //Account Relation To Transaction
    public function transactions()
    {
        return $this->hasMany(Transaction::class,'account_id','id')->orderBy('created_at','DESC');
    }
}
