<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pertemuan 9 - BookTrack Database Migration (Sprint 07)
 * Migration untuk membuat tabel 'books'
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('penulis');
            $table->string('genre')->default('Umum');
            $table->integer('total_halaman')->default(100);
            $table->integer('halaman_dibaca')->default(0);
            $table->string('status')->default('Ingin dibaca');
            $table->float('rating')->default(0.0);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
