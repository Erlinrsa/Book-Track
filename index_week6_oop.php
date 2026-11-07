<?php
/**
 * =========================================================================
 * SPRINT 05 (WEEK 6) - FROM PROCEDURAL TO OBJECT-ORIENTED PHP (OOP)
 * Tema Project : BookTrack - Pelacak Buku & Program Prioritas
 * Sesuai Modul Pertemuan 6: Class, Object, Property, Method, Constructor,
 * dan Encapsulation (Private Properties + Public Getters)
 * =========================================================================
 */

/* =========================================================================
 * TASK 01 & TASK 02: CREATE CLASS, PROPERTIES, METHODS, CONSTRUCTOR, & ENCAPSULATION
 * ========================================================================= */

/**
 * Class User
 * Merepresentasikan data profil pengguna / pembaca (serupa dengan class President)
 */
class User {
    // Encapsulation: Property private (hanya dapat diakses melalui method di dalam class)
    private string $name;
    private string $role;
    private int $yearlyTarget;

    /**
     * Constructor: Menginisialisasi data saat object dibuat
     */
    public function __construct(string $name, string $role = "Pembaca Prioritas", int $yearlyTarget = 12) {
        $this->name         = $name;
        $this->role         = $role;
        $this->yearlyTarget = $yearlyTarget;
    }

    // Getter methods (Public interface)
    public function getName(): string {
        return $this->name;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function getYearlyTarget(): int {
        return $this->yearlyTarget;
    }

    /**
     * Method untuk menampilkan profil ringkas (sesuai contoh di slide: getProfile())
     */
    public function getProfile(): string {
        return $this->name . " · " . $this->role;
    }

    /**
     * Method untuk menghasilkan inisial avatar (misal: "Erlin Mariska" -> "EF")
     */
    public function getInitials(): string {
        $words = explode(" ", $this->name);
        $initials = "";
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper(substr($w, 0, 1));
        }
        return $initials ?: "BK";
    }

    /**
     * Method untuk menentukan sapaan dinamis berdasarkan jam server
     */
    public function getGreeting(): string {
        $hour = (int)date("H");
        if ($hour >= 5 && $hour < 11) {
            return "Pagi";
        } elseif ($hour >= 11 && $hour < 15) {
            return "Siang";
        } elseif ($hour >= 15 && $hour < 18) {
            return "Sore";
        } else {
            return "Malam";
        }
    }
}

/**
 * Class Book
 * Merepresentasikan satu entitas Buku / Program Prioritas
 */
class Book {
    // Encapsulation: Semua data dibungkus sebagai property private
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

    /**
     * Constructor untuk inisialisasi seluruh property buku
     */
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

    // Public Getters
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
     * Method bisnis: Menghitung persentase progres membaca
     */
    public function getProgressPercentage(): int {
        return (int)min(100, round(($this->currentPage / $this->totalPages) * 100));
    }

    /**
     * Method bisnis: Menentukan styling badge status dengan conditional
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
     * Method bisnis: Menghasilkan markup rating bintang emas
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

/**
 * Class BookTracker
 * Bertindak sebagai Controller / Service yang mengelola kumpulan object buku dan profil user
 */
class BookTracker {
    private User $user;
    /** @var Book[] */
    private array $books = [];

    public function __construct(User $user, array $books = []) {
        $this->user  = $user;
        $this->books = $books;
    }

    public function getUser(): User {
        return $this->user;
    }

    /**
     * @return Book[]
     */
    public function getBooks(): array {
        return $this->books;
    }

    public function getTotalBooks(): int {
        return count($this->books);
    }

    public function getTotalPagesRead(): int {
        $total = 0;
        foreach ($this->books as $book) {
            $total += $book->getCurrentPage();
        }
        return $total;
    }

    public function getFinishedCount(): int {
        $count = 0;
        foreach ($this->books as $book) {
            if ($book->getStatus() === "Selesai") {
                $count++;
            }
        }
        return $count;
    }

    public function getAverageRating(): string {
        $total = 0;
        $ratedCount = 0;
        foreach ($this->books as $book) {
            if ($book->getRating() > 0) {
                $total += $book->getRating();
                $ratedCount++;
            }
        }
        return $ratedCount > 0 ? number_format($total / $ratedCount, 1) : "5.0";
    }

    public function getTargetPercentage(): int {
        $target = $this->user->getYearlyTarget();
        if ($target <= 0) return 0;
        return (int)min(100, round(($this->getFinishedCount() / $target) * 100));
    }
}

/* =========================================================================
 * INSTANSIASI OBJECT (OOP INITIALIZATION)
 * Membuat object nyata dari Class yang telah didefinisikan di atas
 * ========================================================================= */

// 1. Instansiasi Object User
$currentUser = new User("Erlin Mariska", "Pembaca Prioritas", 12);

// 2. Instansiasi Daftar Object Book
$bookObjects = [
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

// 3. Instansiasi Object Tracker yang mengelola seluruh data
$tracker = new BookTracker($currentUser, $bookObjects);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BookTrack - Object-Oriented PHP (Week 6)</title>
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              500: '#2563eb',
              600: '#1d6cdb',
              700: '#1d4ed8'
            }
          }
        }
      }
    }
  </script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/styles.css" />
</head>
<body class="bg-[#f8fafc] dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen antialiased flex flex-col lg:flex-row">

  <!-- ========================================================================= -->
  <!-- SIDEBAR NAVIGATION (OOP Output: Data diambil dari Object $tracker->getUser()) -->
  <!-- ========================================================================= -->
  <aside id="sidebar" class="w-64 bg-white dark:bg-slate-900 border-r border-slate-100 dark:border-slate-800 flex flex-col justify-between p-6 fixed lg:sticky top-0 h-screen z-40">
    <div>
      <!-- Brand Logo -->
      <div class="mb-8 pt-2 flex items-center justify-between">
        <a href="index.php" class="font-serif font-black text-2xl text-blue-950 dark:text-blue-400 tracking-tight">
          BookTrack
        </a>
        <span class="text-[10px] bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 font-bold px-2 py-0.5 rounded-full">
          Sprint 05 OOP
        </span>
      </div>

      <!-- User Profile Card (TASK 03: Data dari Method Object User) -->
      <div class="mb-6 p-3.5 bg-slate-50 dark:bg-slate-800/70 border border-slate-100 dark:border-slate-700/60 rounded-xl flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-600 to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-sm flex-shrink-0">
          <?= htmlspecialchars($tracker->getUser()->getInitials()); ?>
        </div>
        <div class="min-w-0">
          <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
            <?= htmlspecialchars($tracker->getUser()->getName()); ?>
          </p>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
            <?= htmlspecialchars($tracker->getUser()->getRole()); ?>
          </p>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="space-y-1.5">
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 transition">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          <span>Program OOP</span>
        </a>
        <a href="index.html" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span>Versi Client-Side (W4)</span>
        </a>
      </nav>
    </div>

    <!-- Info Server PHP -->
    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400">
      <p class="font-semibold text-slate-700 dark:text-slate-300">Sprint 05: OOP PHP</p>
      <p class="text-[10px] text-slate-400 mt-0.5">Classes: User, Book, Tracker</p>
    </div>
  </aside>

  <!-- ========================================================================= -->
  <!-- MAIN CONTENT AREA -->
  <!-- ========================================================================= -->
  <main class="flex-1 p-6 sm:p-10 max-w-7xl mx-auto w-full">
    
    <!-- Header Salam Dinamis (TASK 03: Data dipanggil dari Method Object) -->
    <div class="mb-8">
      <div class="inline-flex items-center gap-2 text-xs font-semibold tracking-wider uppercase text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200/60 dark:border-indigo-800 px-3 py-1 rounded-full">
        <span>● Object-Oriented Architecture (Week 6)</span>
      </div>
      
      <h1 class="text-3xl sm:text-4xl font-serif font-bold text-slate-900 dark:text-white tracking-tight mt-3">
        Selamat <?= $tracker->getUser()->getGreeting(); ?>, <?= htmlspecialchars($tracker->getUser()->getName()); ?>.
      </h1>
      <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm sm:text-base">
        Profil: <strong><?= htmlspecialchars($tracker->getUser()->getProfile()); ?></strong> — Konten di bawah di-render secara dinamis menggunakan <strong>Class & Object PHP</strong>.
      </p>
    </div>

    <!-- ========================================================================= -->
    <!-- 4 KARTU STATISTIK (Dihitung Melalui Method Object BookTracker) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-10">
      
      <!-- Card 1: Total Buku -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white"><?= $tracker->getTotalBooks(); ?></p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Total Objek Buku</p>
      </div>

      <!-- Card 2: Halaman Terbaca -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white"><?= number_format($tracker->getTotalPagesRead(), 0, ',', '.'); ?></p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Halaman Terbaca</p>
      </div>

      <!-- Card 3: Target Selesai -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-emerald-600 dark:text-emerald-400">
          <?= $tracker->getFinishedCount(); ?> / <?= $tracker->getUser()->getYearlyTarget(); ?>
        </p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">
          Target Tahun Ini (<?= $tracker->getTargetPercentage(); ?>%)
        </p>
      </div>

      <!-- Card 4: Rata-Rata Rating -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-amber-500"><?= $tracker->getAverageRating(); ?> ⭐</p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Rata-rata Rating</p>
      </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TASK 03: GENERATE DYNAMIC CONTENT WITH OBJECT -->
    <!-- SSR Dynamic Grid Cards: Menampilkan Object Book Melalui Looping -->
    <!-- ========================================================================= -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 border border-slate-100 dark:border-slate-700/60 shadow-sm mb-10">
      
      <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-100 dark:border-slate-700 gap-2">
        <div>
          <h2 class="text-xl font-bold text-slate-900 dark:text-white">Program Prioritas (Generated from Book Objects)</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Setiap kartu di bawah adalah representasi dari instance <code>new Book()</code> dengan pemanggilan method & encapsulation.
          </p>
        </div>
        <span class="text-xs font-semibold px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg self-start sm:self-auto">
          <?= $tracker->getTotalBooks(); ?> Objek Aktif
        </span>
      </div>

      <!-- Grid Cards: Loop Array of Book Objects -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($tracker->getBooks() as $index => $book): ?>
          <?php
            // Task 02 & Task 03: Mengakses data & perilaku object melalui method
            $no        = $index + 1;
            $badge     = $book->getStatusBadge(); // Method conditional badge
            $progress  = $book->getProgressPercentage(); // Method persentase
            $starsHtml = $book->renderRatingStars(); // Method rating stars
          ?>
          
          <!-- Card Item Hasil Olahan Object -->
          <div class="border border-slate-100 dark:border-slate-700/70 bg-slate-50/50 dark:bg-slate-850 rounded-2xl p-5 flex flex-col justify-between hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-700 transition-all duration-300">
            
            <div>
              <!-- Top Row: Penomoran + Badge Status -->
              <div class="flex items-center justify-between mb-3">
                <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                  #<?= $no; ?>
                </span>
                <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full border <?= $badge['class']; ?>">
                  <?= $badge['text']; ?>
                </span>
              </div>

              <!-- Mini Cover & Judul -->
              <div class="flex items-start gap-3.5 mb-4">
                <div class="w-14 h-20 rounded-lg bg-gradient-to-br <?= $book->getGradient(); ?> flex-shrink-0 p-1.5 text-white flex flex-col justify-between shadow-sm relative overflow-hidden">
                  <?php if ($book->getCoverUrl()): ?>
                    <img src="<?= htmlspecialchars($book->getCoverUrl()); ?>" class="absolute inset-0 w-full h-full object-cover rounded-lg" alt="" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent rounded-lg pointer-events-none"></div>
                  <?php endif; ?>
                  <span class="text-[8px] font-bold uppercase tracking-wider opacity-80 truncate z-10"><?= htmlspecialchars($book->getGenre()); ?></span>
                  <span class="text-[8px] opacity-75 truncate z-10"><?= htmlspecialchars($book->getAuthor()); ?></span>
                </div>

                <div class="min-w-0 flex-1">
                  <h3 class="font-bold text-base text-slate-900 dark:text-white leading-snug line-clamp-2">
                    <?= htmlspecialchars($book->getTitle()); ?>
                  </h3>
                  <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Penulis: <span class="font-medium text-slate-700 dark:text-slate-300"><?= htmlspecialchars($book->getAuthor()); ?></span>
                  </p>
                  <div class="mt-2">
                    <?= $starsHtml; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bottom Progress & Metrik -->
            <div class="pt-3 border-t border-slate-200/60 dark:border-slate-700/60">
              <div class="flex justify-between items-center text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">
                <span><?= $book->getCurrentPage(); ?> / <?= $book->getTotalPages(); ?> hlm</span>
                <span class="font-bold <?= $progress === 100 ? 'text-emerald-600' : 'text-indigo-600'; ?>"><?= $progress; ?>%</span>
              </div>
              
              <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                <div class="<?= $progress === 100 ? 'bg-emerald-500' : 'bg-indigo-600'; ?> h-2 rounded-full transition-all duration-500" style="width: <?= $progress; ?>%"></div>
              </div>
            </div>

          </div>
        <?php endforeach; ?>
      </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TABEL REKAPITULASI DARI OBJECT (OOP Table Iteration) -->
    <!-- ========================================================================= -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 border border-slate-100 dark:border-slate-700/60 shadow-sm overflow-x-auto">
      <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Tabel Rekapitulasi Data Objek (OOP Iteration)</h3>
      <table class="w-full text-left text-xs sm:text-sm">
        <thead>
          <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-400 uppercase text-[11px] tracking-wider">
            <th class="pb-3 font-semibold">No</th>
            <th class="pb-3 font-semibold">Judul Buku (getTitle)</th>
            <th class="pb-3 font-semibold">Penulis (getAuthor)</th>
            <th class="pb-3 font-semibold">Genre (getGenre)</th>
            <th class="pb-3 font-semibold">Halaman</th>
            <th class="pb-3 font-semibold">Status (getStatusBadge)</th>
            <th class="pb-3 font-semibold text-right">Rating (getRating)</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
          <?php foreach ($tracker->getBooks() as $i => $b): ?>
            <?php $stBadge = $b->getStatusBadge(); ?>
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
              <td class="py-3 font-bold text-slate-500"><?= $i + 1; ?></td>
              <td class="py-3 font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($b->getTitle()); ?></td>
              <td class="py-3 text-slate-500 dark:text-slate-400"><?= htmlspecialchars($b->getAuthor()); ?></td>
              <td class="py-3"><span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 rounded text-xs"><?= $b->getGenre(); ?></span></td>
              <td class="py-3 text-slate-500"><?= $b->getTotalPages(); ?> hlm</td>
              <td class="py-3"><span class="px-2 py-0.5 rounded-full border text-[11px] font-medium <?= $stBadge['class']; ?>"><?= $stBadge['text']; ?></span></td>
              <td class="py-3 text-right font-bold text-amber-500"><?= $b->getRating() > 0 ? $b->getRating() . ' ★' : '-'; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </main>

</body>
</html>
