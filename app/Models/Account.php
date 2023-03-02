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

    public function accountUsers(){
        return $this->hasMany(AccountUser::class,'account_id');
    }
    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }
}
