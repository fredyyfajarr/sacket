<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'location', 'start_date', 'end_date', 'image'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
