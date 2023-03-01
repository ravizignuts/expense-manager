<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'first_name',
        'last_name',
        'email'
    ];
    public function transactions(){
        return $this->hasMany(Transaction::class,'account_user_id');
    }
}
