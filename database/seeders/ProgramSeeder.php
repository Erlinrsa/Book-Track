<?php

namespace Database\Seeders;

use App\Models\Program;

class ProgramSeeder
{
    /**
     * Run the database seeds.
     * Sesuai Slide 13 & 14 Demo PresidentKu
     */
    public function run(): void
    {
        $programs = [
            [
                'nama' => 'Pendidikan',
                'deskripsi' => 'Meningkatkan akses pendidikan berkualitas dan beasiswa untuk seluruh generasi bangsa.'
            ],
            [
                'nama' => 'Kesehatan',
                'deskripsi' => 'Meningkatkan layanan kesehatan, fasilitas medis, dan jaminan kesehatan merata.'
            ],
            [
                'nama' => 'Lingkungan',
                'deskripsi' => 'Menjaga kelestarian lingkungan hidup dan mempercepat transisi energi hijau terbarukan.'
            ],
            [
                'nama' => 'Infrastruktur',
                'deskripsi' => 'Membangun infrastruktur transportasi dan konektivitas digital yang terintegrasi.'
            ]
        ];

        foreach ($programs as $prog) {
            Program::create($prog);
        }
    }
}
