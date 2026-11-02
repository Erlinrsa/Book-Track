<?php
/**
 * Class Book (Model)
 * Sesuai Modul Pertemuan 7 (Sprint 06 MVC)
 * Merepresentasikan satu entitas Buku / Program Prioritas dengan Encapsulation
 */
class Book {
    private int $id;
    private string $title;
    private string $author;
    private string $genre;
    private int $totalPages;
    private int $currentPage;
    private string $status;
    private float $rating;
    private string $gradient;
    private string $coverUrl;

    public function __construct(
        int $id,
        string $title,
        string $author,
        string $genre,
        int $totalPages,
        int $currentPage,
        string $status,
        float $rating,
        string $gradient = "from-slate-700 to-slate-900",
        string $coverUrl = ""
    ) {
        $this->id          = $id;
        $this->title       = $title;
        $this->author      = $author;
        $this->genre       = $genre;
        $this->totalPages  = max(1, $totalPages);
        $this->currentPage = max(0, min($currentPage, $totalPages));
        $this->status      = $status;
        $this->rating      = $rating;
        $this->gradient    = $gradient;
        $this->coverUrl    = $coverUrl;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getAuthor(): string {
        return $this->author;
    }

    public function getGenre(): string {
        return $this->genre;
    }

    public function getTotalPages(): int {
        return $this->totalPages;
    }

    public function getCurrentPage(): int {
        return $this->currentPage;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getRating(): float {
        return $this->rating;
    }

    public function getGradient(): string {
        return $this->gradient;
    }

    public function getCoverUrl(): string {
        return $this->coverUrl;
    }

    /**
     * Menghitung persentase progres membaca
     */
    public function getProgressPercentage(): int {
        return (int)min(100, round(($this->currentPage / $this->totalPages) * 100));
    }

    /**
     * Menentukan styling badge status
     */
    public function getStatusBadge(): array {
        if ($this->status === "Selesai") {
            return [
                "text"  => "Selesai",
                "class" => "bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-800"
            ];
        } elseif ($this->status === "Sedang dibaca") {
            return [
                "text"  => "Sedang dibaca",
                "class" => "bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-800"
            ];
        } else {
            return [
                "text"  => "Ingin dibaca",
                "class" => "bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700"
            ];
        }
    }

    /**
     * Menghasilkan markup rating bintang
     */
    public function renderRatingStars(): string {
        if ($this->rating <= 0) {
            return '<span class="text-xs text-slate-400">Belum dinilai</span>';
        }
        $rounded = round($this->rating);
        $html = '<div class="flex items-center gap-0.5 text-amber-400 text-xs">';
        for ($i = 1; $i <= 5; $i++) {
            $html .= ($i <= $rounded) ? '<span>★</span>' : '<span class="text-slate-300 dark:text-slate-600">★</span>';
        }
        $html .= '<span class="text-[11px] text-slate-500 dark:text-slate-400 ml-1 font-semibold">' . number_format($this->rating, 1) . '</span>';
        $html .= '</div>';
        return $html;
    }
}
