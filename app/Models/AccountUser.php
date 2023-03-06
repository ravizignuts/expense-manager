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
    /**
     * Define has Many Relation with Transaction model
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_user_id');
    }
    /**
     * Define Belongs to Relation with Account model
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
