<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MENIMPA migration bawaan Laravel (nama fail sama).
 * Setara tabel `users` P4: username, full_name, role (admin/staff).
 * Kolom password_hash P4 -> `password` (konvensyen Laravel; hash bcrypt PHP kompatibel).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('full_name', 100);
            $table->string('password');
            $table->enum('role', ['admin', 'staff'])->default('staff');
            $table->rememberToken();
            $table->timestamps();
        });

        // Diperlukan karena SESSION_DRIVER=database (default Laravel 11/12).
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
