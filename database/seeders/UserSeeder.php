<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ===============================================
        // TAMBAHKAN BARIS INI UNTUK MEMBERSIHKAN CACHE
        // ===============================================
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat user admin dan berikan role 'admin'
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('admin');

        // Buat user biasa dan berikan role 'user'
        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
        ]);
        $user->assignRole('user');

        // Buat user scanner dan berikan role 'scanner'
        $scanner = User::factory()->create([
            'name' => 'Scanner',
            'email' => 'scanner@example.com',
        ]);
        $scanner->assignRole('scanner');
    }
}
