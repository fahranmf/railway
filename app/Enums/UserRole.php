<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel, HasColor
{
    case Admin = 'admin';
    case Staff = 'staff';
    case Customer = 'customer'; // <--- INI TAMBAHAN PENTING

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => 'Admin (Pemilik)',
            self::Staff => 'Staff (Kasir)',
            self::Customer => 'Pelanggan', // Label untuk User Biasa
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Admin => 'success', // Hijau
            self::Staff => 'info',    // Biru
            self::Customer => 'gray', // Abu-abu (Netral)
        };
    }
}
