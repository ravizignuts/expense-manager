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
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];
    /**
     * Define has Many Relation with AccountUser model
     */
    public function accountUsers()
    {
        return $this->hasMany(AccountUser::class, 'account_id');
    }
    /**
     * Define has Many Relation with Transaction model
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id')->orderBy('created_at','DESC')->whereDate('created', '=', date('d-m-y'));
    }
    /**
     * Define Belongs to Relation with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
