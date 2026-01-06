<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBatch extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expired_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    // Relasi balik ke Produk
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
