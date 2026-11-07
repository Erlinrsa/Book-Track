<?php
/**
 * Class BookModel (Model & Data Access Layer)
 * Sesuai Modul Pertemuan 7 (Slide 4: Data + Data Access + Business Data)
 * Bertanggung jawab mengelola sumber data, query filtering, dan kalkulasi statistik
 */
class BookModel {
    /**
     * Mengambil instance profil pembaca saat ini
     */
    public static function getUser(): User {
        return new User("Erlin Mariska", "Pembaca Prioritas", 12);
    }

    /**
     * Mengambil seluruh data buku (Data Source / Simulation Database)
     * @return Book[]
     */
    public static function getAllBooks(): array {
        return [
            new Book(
                1,
                "Filosofi Teras",
                "Henry Manampiring",
                "Filosofi",
                320,
                320,
                "Selesai",
                5.0,
                "from-slate-700 to-slate-900",
                ""
            ),
            new Book(
                2,
                "Atomic Habits",
                "James Clear",
                "Self-Improvement",
                320,
                240,
                "Sedang dibaca",
                4.8,
                "from-blue-700 to-blue-950",
                "https://covers.openlibrary.org/b/id/12843460-M.jpg"
            ),
            new Book(
                3,
                "Laut Bercerita",
                "Leila S. Chudori",
                "Fiksi",
                379,
                150,
                "Sedang dibaca",
                4.9,
                "from-cyan-800 to-blue-950",
                "https://covers.openlibrary.org/b/id/10543666-M.jpg"
            ),
            new Book(
                4,
                "Clean Code",
                "Robert C. Martin",
                "Teknologi",
                464,
                464,
                "Selesai",
                5.0,
                "from-emerald-700 to-teal-950",
                ""
            ),
            new Book(
                5,
                "Sapiens: Riwayat Singkat Umat Manusia",
                "Yuval Noah Harari",
                "Non-Fiksi",
                498,
                0,
                "Ingin dibaca",
                0.0,
                "from-amber-700 to-orange-950",
                "https://covers.openlibrary.org/b/id/8231990-M.jpg"
            ),
            new Book(
                6,
                "Janji",
                "Tere Liye",
                "Fiksi",
                488,
                0,
                "Ingin dibaca",
                0.0,
                "from-indigo-800 to-purple-950",
                ""
            )
        ];
    }

    /**
     * Memfilter data buku berdasarkan status bacaan (Request Handling)
     * @param string $status
     * @return Book[]
     */
    public static function getFilteredBooks(string $status = 'all'): array {
        $all = self::getAllBooks();
        if ($status === 'all' || empty($status)) {
            return $all;
        }

        return array_values(array_filter($all, function (Book $book) use ($status) {
            return strtolower($book->getStatus()) === strtolower($status);
        }));
    }

    /**
     * Menghitung metrik statistik dari kumpulan objek buku
     */
    public static function getStatistics(array $books, User $user): array {
        $totalBooks      = count($books);
        $totalPagesRead  = 0;
        $finishedCount   = 0;
        $totalRating     = 0;
        $ratedCount      = 0;

        foreach ($books as $book) {
            $totalPagesRead += $book->getCurrentPage();
            if ($book->getStatus() === "Selesai") {
                $finishedCount++;
            }
            if ($book->getRating() > 0) {
                $totalRating += $book->getRating();
                $ratedCount++;
            }
        }

        $avgRating = $ratedCount > 0 ? number_format($totalRating / $ratedCount, 1) : "5.0";
        $target    = $user->getYearlyTarget();
        $targetPct = $target > 0 ? (int)min(100, round(($finishedCount / $target) * 100)) : 0;

        return [
            'totalBooks'       => $totalBooks,
            'totalPagesRead'   => $totalPagesRead,
            'finishedCount'    => $finishedCount,
            'averageRating'    => $avgRating,
            'targetPercentage' => $targetPct
        ];
    }
}
