<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'account_user_id',
        'type',
        'date',
        'category',
        'amount'
    ];
    /**
     * Define Belongs to Relation with AccountUser model
     */
    public function accountUser()
    {
        return $this->belongsTo(AccountUser::class, 'account_user_id');
    }
    /**
     * Define Belongs to Relation with Account model
     */
    public function account(){
        return $this->belongsTo(Account::class,'account_id');
    }
}
