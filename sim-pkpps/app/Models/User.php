<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ══════════════════ HELPER METHODS ══════════════════

    /**
     * Cek apakah user adalah admin (semua role admin)
     */
    public function isAdmin()
    {
        return in_array($this->role, ['super_admin', 'akademik', 'pamong']);
    }

    /**
     * Cek apakah user adalah super admin
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Cek apakah user adalah akademik
     */
    public function isAkademik()
    {
        return $this->role === 'akademik';
    }

    /**
     * Cek apakah user adalah pamong
     */
    public function isPamong()
    {
        return $this->role === 'pamong';
    }

    /**
     * Cek apakah user memiliki salah satu role yang diberikan.
     * Contoh: $user->hasRole('super_admin', 'akademik')
     */
    public function hasRole()
    {
        $roles = func_get_args();
        return in_array($this->role, $roles);
    }
}