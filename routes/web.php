<?php

use App\Models\Program;
use App\Models\Book;
use Illuminate\Support\Facades\Route;

/**
 * =========================================================================
 * PERTEMUAN 9 - ROUTING LARAVEL (SPRINT 07)
 * Sesuai Slide 3, Slide 11, dan Slide 12
 * Catatan modul: Belum fokus Controller/Validation mendalam (materi Pekan 10)
 * =========================================================================
 */

// 1. Root & READ: Menampilkan semua data program dari database (Slide 12)
Route::get('/', function () {
    return redirect('/programs');
});

Route::get('/programs', function () {
    // READ: Eloquent ORM mengambil seluruh data dari tabel 'programs'
    $programs = Program::all();
    return view('programs.index', compact('programs'));
});

// 2. CREATE: Menampilkan form tambah program (Slide 12)
Route::get('/programs/create', function () {
    return view('programs.create');
});

// 3. STORE (CREATE): Menyimpan data baru ke database via Eloquent (Slide 11 & 12)
Route::post('/programs', function () {
    Program::create([
        'nama' => request('nama'),
        'deskripsi' => request('deskripsi'),
    ]);
    return redirect('/programs');
});

// 4. EDIT (UPDATE): Mengambil data berdasarkan ID untuk diedit (Slide 11 & 12)
Route::get('/programs/{id}/edit', function ($id) {
    // Eloquent ORM: Program::find($id)
    $program = Program::find($id);
    if (!$program) {
        return redirect('/programs');
    }
    return view('programs.edit', compact('program'));
});

// 5. UPDATE: Menyimpan perubahan data ke database (Slide 12)
Route::post('/programs/{id}/update', function ($id) {
    $program = Program::find($id);
    if ($program) {
        $program->update([
            'nama' => request('nama'),
            'deskripsi' => request('deskripsi'),
        ]);
    }
    return redirect('/programs');
});

// 6. DELETE: Menghapus data dari database via Eloquent (Slide 12 & 13)
Route::post('/programs/{id}/delete', function ($id) {
    $program = Program::find($id);
    if ($program) {
        $program->delete();
    }
    return redirect('/programs');
});

// 7. UJI ELOQUENT ORM (Khusus Pengujian Slide 11 Task 02)
Route::get('/test-eloquent', function () {
    // 1. Mengambil semua data
    $allPrograms = Program::all();

    // 2. Mengambil satu data berdasarkan id
    $firstProgram = Program::find(1);

    // 3. Uji create jika tabel masih kosong
    if (count($allPrograms) === 0) {
        Program::create([
            'nama' => 'Pendidikan',
            'deskripsi' => 'Program pendidikan PresidentKu'
        ]);
        $allPrograms = Program::all();
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Uji Eloquent ORM Berhasil (Slide 11)',
        'all_programs_count' => count($allPrograms),
        'programs' => array_map(fn($p) => $p->toArray(), $allPrograms),
        'single_program' => $firstProgram ? $firstProgram->toArray() : null,
    ]);
});
