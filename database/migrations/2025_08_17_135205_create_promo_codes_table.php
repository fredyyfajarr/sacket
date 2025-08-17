<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode uniknya, misal: HEMAT20
            $table->enum('type', ['fixed', 'percentage']); // Jenis diskon: tetap atau persen
            $table->decimal('value', 10, 2); // Nilai diskonnya
            $table->integer('max_uses')->nullable(); // Batas total penggunaan (opsional)
            $table->integer('uses')->default(0); // Berapa kali sudah digunakan
            $table->timestamp('expires_at')->nullable(); // Tanggal kadaluarsa (opsional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
