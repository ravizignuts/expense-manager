<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\ResponseWithStatus;

class AccountUser extends Model
{
    use HasFactory, ResponseWithStatus;
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
        return $this->hasMany(Transaction::class, 'account_user_id')->orderBy('date','DESC');
    }
    /**
     * Define Belongs to Relation with Account model
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
