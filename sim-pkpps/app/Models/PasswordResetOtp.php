<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $table = 'password_reset_otps';

    protected $fillable = [
        'email',
        'otp',
        'expired_at',
        'is_verified',
    ];

    protected $casts = [
        'expired_at'  => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * Cek apakah OTP sudah expired
     */
    public function isExpired(): bool
    {
        return now()->greaterThan($this->expired_at);
    }
}
