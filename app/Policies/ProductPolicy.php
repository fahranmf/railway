<?php

namespace App\Policies;

use App\Enums\UserRole; // Panggil Enum punya Arga
use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Siapa yang boleh melihat daftar obat?
     * SEMUA BOLEH (Admin & Staff)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Siapa yang boleh melihat detail obat?
     * SEMUA BOLEH
     */
    public function view(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Siapa yang boleh menambah obat baru?
     * SEMUA BOLEH (Asumsi Staff boleh input barang masuk)
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Siapa yang boleh mengedit obat?
     * SEMUA BOLEH
     */
    public function update(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Siapa yang boleh MENGHAPUS obat?
     * ⛔ HANYA ADMIN! (Staff Dilarang)
     */
    public function delete(User $user, Product $product): bool
    {
        // Cek apakah role user adalah Admin
        return $user->role === UserRole::Admin;
    }

    /**
     * Siapa yang boleh Restore?
     * ⛔ HANYA ADMIN
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Siapa yang boleh Hapus Permanen?
     * ⛔ HANYA ADMIN
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->role === UserRole::Admin;
    }
}
