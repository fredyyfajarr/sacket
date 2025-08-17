<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'order_number',
        'customer_name',
        'customer_email',
        'total_price',
        'status',
        'promo_code_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Tambahkan relasi ini
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
