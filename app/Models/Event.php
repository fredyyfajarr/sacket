<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'location',
        'start_date',
        'end_date',
        'image',
        'is_published' // <--- DITAMBAHKAN
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_published' => 'boolean', // <--- DITAMBAHKAN
    ];

    /**
     * Scope Helper: Hanya ambil event yang sudah dipublish.
     * Cara pakainya: Event::published()->get();
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('images')
            ->singleFile();
    }

    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
