<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SantriAccount extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'santri_accounts';

    protected $fillable = [
        'id_santri',
        'username',
        'password',
        'role',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // HAPUS 'password' => 'hashed' — hanya Laravel 10+
    protected $casts = [
        'last_login' => 'datetime',
    ];

    // Ganti dengan mutator manual
    public function setPasswordAttribute(string $value): void
    {
        // Cegah double hash
        if (
            !str_starts_with($value, '$2y$') &&
            !str_starts_with($value, '$argon2i$') &&
            !str_starts_with($value, '$argon2id$')
        ) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    public function isSantri(): bool
    {
        return $this->role === 'santri';
    }

    public function isWali(): bool
    {
        return $this->role === 'wali';
    }
}