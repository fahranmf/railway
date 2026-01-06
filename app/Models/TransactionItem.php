<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ProductBatch;

class TransactionItem extends Model
{
    use HasFactory;

    protected $table = 'transaction_items';

    // Guarded kosong agar mass assignment lancar
    protected $guarded = [];

    // Casts memastikan angka desimal & integer terbaca benar
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR (PENTING UNTUK PERBAIKAN)
    |--------------------------------------------------------------------------
    */

    /**
     * JEMBATAN: View Anda memanggil $item->qty
     * Database Anda punya kolom 'quantity'
     * Fungsi ini menghubungkannya.
     */
    public function getQtyAttribute()
    {
        return $this->attributes['quantity'] ?? 0;
    }

    /**
     * HELPER: Ambil nama produk secara aman.
     * Cek Batch dulu -> Cek Produk Induk -> Default
     */
    public function getProductNameAttribute()
    {
        // 1. Cek dari Batch (Jika ada)
        if ($this->batch && $this->batch->product) {
            return $this->batch->product->name;
        }

        // 2. Cek dari Produk Induk (Backup jika batch dihapus)
        if ($this->product) {
            return $this->product->name;
        }

        return 'Item Tidak Dikenal';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $item) {
            // Jika `product_id` tidak diset tetapi `product_batch_id` ada,
            // ambil product_id dari batch terkait sehingga constraint tidak gagal.
            if (empty($item->product_id) && !empty($item->product_batch_id)) {
                $batch = ProductBatch::find($item->product_batch_id);
                if ($batch) {
                    $item->product_id = $batch->product_id;
                }
            }
        });
    }
}
