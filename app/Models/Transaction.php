<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $guarded = ['id'];

    protected $appends = ['invoice_code'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'paid_at'    => 'datetime',
        'total_amount' => 'decimal:2',
        'status' => 'string',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * [PERBAIKAN UTAMA] ACCESSOR: total_amount
     * Memperbaiki tampilan 'Rp 0' pada transaksi lama.
     */
    public function getTotalAmountAttribute($value)
    {
        // 1. Jika di database nilainya sudah benar (> 0), gunakan nilai database.
        if ($value > 0) {
            return $value;
        }

        // 2. Jika nilai 0 (Error lama), hitung otomatis dari item.
        // Fungsi ini menjumlahkan (qty x price) setiap item.
        return $this->items->sum(function($item) {
            // Mengambil quantity (Support kolom 'quantity' atau accessor 'qty')
            $qty = $item->quantity ?? $item->qty ?? 0;
            return $qty * $item->price;
        });
    }

    /**
     * ACCESSOR: invoice_code
     */
    public function getInvoiceCodeAttribute(): string
    {
        if (!$this->id) {
            return 'INV-PENDING';
        }
        return 'INV-' . $this->created_at->format('Ymd') . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES (Query Filters)
    |--------------------------------------------------------------------------
    */

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
}
