<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // 4. Tambahkan method ini
    public function canAccessPanel(Panel $panel): bool
    {
        // Izinkan akses jika role user adalah 'admin'
        return $this->role === 'admin';
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';

        // Ambil huruf pertama dari kata pertama
        if (isset($words[0][0])) {
            $initials .= strtoupper($words[0][0]);
        }

        // Jika ada lebih dari satu kata, ambil huruf pertama dari kata terakhir
        if (count($words) > 1 && isset($words[count($words) - 1][0])) {
            $initials .= strtoupper($words[count($words) - 1][0]);
        }

        return $initials;
    }
}
