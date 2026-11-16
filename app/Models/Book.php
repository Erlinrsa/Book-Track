<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pertemuan 9 - BookTrack Model (Sprint 07)
 * Model Eloquent untuk entitas Book
 */
class Book extends Model
{
    protected $table = 'books';

    protected $fillable = [
        'judul',
        'penulis',
        'genre',
        'total_halaman',
        'halaman_dibaca',
        'status',
        'rating',
        'deskripsi'
    ];
}
