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
     * Define Belongs to Relation with User model
     */
    public function user()
    {
        return $this->belongsTo(AccountUser::class, 'account_user_id');
    }
}
