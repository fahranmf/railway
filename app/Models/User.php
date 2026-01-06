<?php

namespace App\Models;

use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    // 1. UPDATE: Tambahkan kolom baru agar bisa disimpan
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'address', // Baru
        'phone',   // Baru
        'city',    // Baru
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class, // Tetap gunakan Enum Anda
        ];
    }

    // 2. UPDATE SECURITY: Jangan biarkan Pelanggan masuk Admin Panel
    public function canAccessPanel(Panel $panel): bool
    {
        // Jika User adalah CUSTOMER, tolak akses (return false)
        // Pastikan di file Enums/UserRole.php Anda sudah ada case 'customer'
        if ($this->role === UserRole::Customer) {
            return false;
        }

        // Selain customer (Admin/Staff) boleh masuk
        return true;
    }
}
