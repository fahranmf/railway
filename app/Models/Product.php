<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Pastikan accessor tersedia saat model diserialisasi
    protected $appends = ['total_stock', 'is_expiring_soon'];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // --- TAMBAHAN BLOCK 4 (DENDY) ---

    // Relasi: Satu Produk punya Banyak Batch
    public function batches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    // Atribut Virtual: Hitung Total Stok dari semua batch
    // Atribut Virtual: Hitung Total Stok dari semua batch
    // Hanya hitung batch yang masa berlakunya setidaknya 7 hari dari sekarang
    public function getTotalStockAttribute(): int
    {
        $threshold = now()->startOfDay()->addDays(7);

        // Gunakan query DB untuk memastikan konsistensi ketika relasi belum eager-loaded
        return (int) $this->batches()
            ->where(function ($q) use ($threshold) {
                $q->whereNull('expired_date')
                  ->orWhereDate('expired_date', '>=', $threshold->toDateString());
            })
            ->sum('stock');
    }

    // Apakah ada batch yang akan kedaluwarsa dalam 7 hari ke depan?
    public function getIsExpiringSoonAttribute(): bool
    {
        $today = now()->startOfDay();
        $threshold = $today->copy()->addDays(7);

        return $this->batches()
            ->whereNotNull('expired_date')
            ->whereDate('expired_date', '>=', $today->toDateString())
            ->whereDate('expired_date', '<=', $threshold->toDateString())
            ->where('stock', '>', 0)
            ->exists();
    }
}
