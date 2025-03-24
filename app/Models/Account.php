<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'account_number', 'balance', 'reserved_balance'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            do {
                $account->account_number = mt_rand(1000000000, 9999999999);
            } while (self::where('account_number', $account->account_number)->exists());
        });
    }

    // Define Relationship: An Account Belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Check if user is new (no deposits)
    public function isNewUser()
    {
        return !Transaction::where('sender_id', $this->user_id)->where('type', 'deposit')->exists();
    }

    // Move $20 to reserved balance (if not already moved)
    public function ensureReservedBalance()
    {
        if ($this->reserved_balance < 20 && $this->balance >= 20) {
            $this->reserved_balance = 20;
            $this->balance -= 20;
            $this->save();
        }
    }
}
