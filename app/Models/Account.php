<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'account_name',
        'account_number',
        'is_default'
    ];

    // public static function booted(){
    //     parent::boot();
    //     static::creating(function (Account $account){

    //         });
    // }
    public function accountUsers(){
        return $this->hasMany(AccountUser::class,'account_id');
    }

}
