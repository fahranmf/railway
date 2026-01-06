<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'payment_status',
        'shipping_address',
        'customer_phone',
    ];

    // Relasi: 1 Order milik 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: 1 Order punya banyak Item
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
