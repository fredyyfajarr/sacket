<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'name', 'price', 'stock', 'sale_start_date', 'sale_end_date'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
