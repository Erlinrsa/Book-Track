<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pertemuan 9 - Task 02: Build Program Model (Sprint 07)
 * Model Eloquent untuk berinteraksi dengan tabel 'programs'
 */
class Program extends Model
{
    /**
     * Nama tabel yang diasosiasikan dengan model (opsional/standar: plural form 'programs')
     *
     * @var string
     */
    protected $table = 'programs';

    /**
     * Atribut yang dapat diisi secara mass-assignment
     * Sesuai checklist Task 02: $fillable dibuat
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'deskripsi'
    ];
}
