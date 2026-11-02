<?php
/**
 * =========================================================================
 * SPRINT 04 (WEEK 5) - IMPLEMENTASI PHP PROSEDURAL (SERVER-SIDE RENDERING)
 * Tema Project : BookTrack - Pelacak Buku & Program Prioritas Bacaan
 * =========================================================================
 */

/* =========================================================================
 * TASK 01: CREATE PHP PAGE & VARIABLES (Sesuai Tema BookTrack)
 * Menginisialisasi variabel PHP yang akan digunakan di seluruh halaman
 * ========================================================================= */
$appName       = "BookTrack";
$pageTitle     = "Pelacak Buku & Program Prioritas Bacaan";
$userName      = "Eko Felix"; // Nama pengguna / pembaca (sesuai tema)
$userRole      = "Pembaca Prioritas";
$targetTahun   = 12;
$tahunIni      = date("Y");

// Conditional untuk menentukan sapaan dinamis berdasarkan jam server
$jamSekarang = (int)date("H");
if ($jamSekarang >= 5 && $jamSekarang < 11) {
    $waktuSapaan = "Pagi";
} elseif ($jamSekarang >= 11 && $jamSekarang < 15) {
    $waktuSapaan = "Siang";
} elseif ($jamSekarang >= 15 && $jamSekarang < 18) {
    $waktuSapaan = "Sore";
} else {
    $waktuSapaan = "Malam";
}

/* =========================================================================
 * TASK 02: PROCESS DATA WITH PHP
 * Menggunakan Array Multidimensi, Functions, Conditional, dan Loop Penomoran
 * ========================================================================= */

// 1. Array Multidimensi: Menyimpan data program prioritas / daftar buku
$daftarBuku = [
    [
        "id"            => 1,
        "judul"         => "Filosofi Teras",
        "penulis"       => "Henry Manampiring",
        "genre"         => "Filosofi",
        "totalHalaman"  => 320,
        "halamanDibaca" => 320,
        "status"        => "Selesai",
        "rating"        => 5.0,
        "prioritas"     => "Tinggi",
        "gradient"      => "from-slate-700 to-slate-900",
        "coverUrl"      => ""
    ],
    [
        "id"            => 2,
        "judul"         => "Atomic Habits",
        "penulis"       => "James Clear",
        "genre"         => "Self-Improvement",
        "totalHalaman"  => 320,
        "halamanDibaca" => 240,
        "status"        => "Sedang dibaca",
        "rating"        => 4.8,
        "prioritas"     => "Sangat Tinggi",
        "gradient"      => "from-blue-700 to-blue-950",
        "coverUrl"      => "https://covers.openlibrary.org/b/id/12843460-M.jpg"
    ],
    [
        "id"            => 3,
        "judul"         => "Laut Bercerita",
        "penulis"       => "Leila S. Chudori",
        "genre"         => "Fiksi",
        "totalHalaman"  => 379,
        "halamanDibaca" => 150,
        "status"        => "Sedang dibaca",
        "rating"        => 4.9,
        "prioritas"     => "Sedang",
        "gradient"      => "from-cyan-800 to-blue-950",
        "coverUrl"      => "https://covers.openlibrary.org/b/id/10543666-M.jpg"
    ],
    [
        "id"            => 4,
        "judul"         => "Clean Code",
        "penulis"       => "Robert C. Martin",
        "genre"         => "Teknologi",
        "totalHalaman"  => 464,
        "halamanDibaca" => 464,
        "status"        => "Selesai",
        "rating"        => 5.0,
        "prioritas"     => "Tinggi",
        "gradient"      => "from-emerald-700 to-teal-950",
        "coverUrl"      => ""
    ],
    [
        "id"            => 5,
        "judul"         => "Sapiens: Riwayat Singkat Umat Manusia",
        "penulis"       => "Yuval Noah Harari",
        "genre"         => "Non-Fiksi",
        "totalHalaman"  => 498,
        "halamanDibaca" => 0,
        "status"        => "Ingin dibaca",
        "rating"        => 0.0,
        "prioritas"     => "Sedang",
        "gradient"      => "from-amber-700 to-orange-950",
        "coverUrl"      => "https://covers.openlibrary.org/b/id/8231990-M.jpg"
    ],
    [
        "id"            => 6,
        "judul"         => "Janji",
        "penulis"       => "Tere Liye",
        "genre"         => "Fiksi",
        "totalHalaman"  => 488,
        "halamanDibaca" => 0,
        "status"        => "Ingin dibaca",
        "rating"        => 0.0,
        "prioritas"     => "Tinggi",
        "gradient"      => "from-indigo-800 to-purple-950",
        "coverUrl"      => ""
    ]
];

// 2. Function PHP: Conditional badge status bacaan
function getStatusBadge($status) {
    if ($status === "Selesai") {
        return [
            "text"  => "Selesai",
            "class" => "bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-800"
        ];
    } elseif ($status === "Sedang dibaca") {
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

// 3. Function PHP: Menghitung persentase progres membaca
function hitungProgres($halamanDibaca, $totalHalaman) {
    if ($totalHalaman <= 0) return 0;
    return min(100, round(($halamanDibaca / $totalHalaman) * 100));
}

// 4. Function PHP: Render bintang rating
function renderRatingStars($rating) {
    if ($rating <= 0) {
        return '<span class="text-xs text-slate-400">Belum dinilai</span>';
    }
    $bulat = round($rating);
    $html = '<div class="flex items-center gap-0.5 text-amber-400 text-xs">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= ($i <= $bulat) ? '<span>★</span>' : '<span class="text-slate-300 dark:text-slate-600">★</span>';
    }
    $html .= '<span class="text-[11px] text-slate-500 dark:text-slate-400 ml-1 font-semibold">' . number_format($rating, 1) . '</span>';
    $html .= '</div>';
    return $html;
}

// 5. Pemrosesan Data Statistik Server-Side (Aggregasi)
$totalBuku          = count($daftarBuku);
$totalHalamanDibaca = 0;
$bukuSelesai        = 0;
$totalRating        = 0;
$jumlahDinilai      = 0;

foreach ($daftarBuku as $buku) {
    $totalHalamanDibaca += $buku['halamanDibaca'];
    if ($buku['status'] === "Selesai") {
        $bukuSelesai++;
    }
    if ($buku['rating'] > 0) {
        $totalRating += $buku['rating'];
        $jumlahDinilai++;
    }
}

$rataRataRating = $jumlahDinilai > 0 ? number_format($totalRating / $jumlahDinilai, 1) : "5.0";
$persenTarget   = round(($bukuSelesai / $targetTahun) * 100);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $appName; ?> - <?php echo $pageTitle; ?></title>
  
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

  <!-- Custom CSS dari Week 4 -->
  <link rel="stylesheet" href="css/styles.css" />
</head>
<body class="bg-[#f8fafc] dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen antialiased flex flex-col lg:flex-row">

  <!-- ========================================================================= -->
  <!-- SIDEBAR NAVIGATION (Melanjutkan Struktur Week 4) -->
  <!-- ========================================================================= -->
  <aside id="sidebar" class="w-64 bg-white dark:bg-slate-900 border-r border-slate-100 dark:border-slate-800 flex flex-col justify-between p-6 fixed lg:sticky top-0 h-screen z-40">
    <div>
      <!-- Brand Logo -->
      <div class="mb-8 pt-2 flex items-center justify-between">
        <a href="index.php" class="font-serif font-black text-2xl text-blue-950 dark:text-blue-400 tracking-tight">
          <?php echo $appName; ?>
        </a>
        <span class="text-[10px] bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 font-bold px-2 py-0.5 rounded-full">
          PHP SSR
        </span>
      </div>

      <!-- User Profile Card di Sidebar (Task 01: Variabel Output) -->
      <div class="mb-6 p-3.5 bg-slate-50 dark:bg-slate-800/70 border border-slate-100 dark:border-slate-700/60 rounded-xl flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm flex-shrink-0">
          <?php echo strtoupper(substr($userName, 0, 2)); ?>
        </div>
        <div class="min-w-0">
          <p class="text-xs font-bold text-slate-900 dark:text-white truncate"><?php echo $userName; ?></p>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate"><?php echo $userRole; ?></p>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="space-y-1.5">
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 transition">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span>Program Prioritas</span>
        </a>
        <a href="index.html" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          <span>Versi Client-Side (W4)</span>
        </a>
      </nav>
    </div>

    <!-- Info Server PHP -->
    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400">
      <p>Sprint 04: Server-Side PHP</p>
      <p class="text-[10px] text-slate-400 mt-0.5">PHP Version: <?php echo phpversion(); ?></p>
    </div>
  </aside>

  <!-- ========================================================================= -->
  <!-- MAIN CONTENT AREA -->
  <!-- ========================================================================= -->
  <main class="flex-1 p-6 sm:p-10 max-w-7xl mx-auto w-full">
    
    <!-- Header Salam Dinamis (Task 01: Sapaan Waktu & Variabel) -->
    <div class="mb-8">
      <span class="text-xs font-semibold tracking-wider uppercase text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-3 py-1 rounded-full">
        Server-Side Rendering (Week 5)
      </span>
      <h1 class="text-3xl sm:text-4xl font-serif font-bold text-slate-900 dark:text-white tracking-tight mt-3">
        Selamat <?php echo $waktuSapaan; ?>, <?php echo $userName; ?>.
      </h1>
      <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm sm:text-base">
        Halaman ini dihasilkan oleh <strong>PHP Prosedural</strong> secara dinamis di server sebelum dikirim ke browser.
      </p>
    </div>

    <!-- ========================================================================= -->
    <!-- 4 KARTU STATISTIK SERVER-SIDE (Task 02 & 03: Agregasi PHP) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-10">
      
      <!-- Card 1: Total Buku -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white"><?php echo $totalBuku; ?></p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Total Buku</p>
      </div>

      <!-- Card 2: Halaman Terbaca -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white"><?php echo number_format($totalHalamanDibaca, 0, ',', '.'); ?></p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Halaman Terbaca</p>
      </div>

      <!-- Card 3: Selesai -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-emerald-600 dark:text-emerald-400"><?php echo $bukuSelesai; ?> / <?php echo $targetTahun; ?></p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Target <?php echo $tahunIni; ?> (<?php echo $persenTarget; ?>%)</p>
      </div>

      <!-- Card 4: Rata-Rata Rating -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-amber-500"><?php echo $rataRataRating; ?> ⭐</p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Rata-rata Rating</p>
      </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TASK 03: BUILD DYNAMIC SERVER-SIDE CONTENT -->
    <!-- SSR Dynamic Grid Cards: Program Prioritas Bacaan (Melanjutkan Struktur Week 4) -->
    <!-- ========================================================================= -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 border border-slate-100 dark:border-slate-700/60 shadow-sm mb-10">
      
      <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-100 dark:border-slate-700 gap-2">
        <div>
          <h2 class="text-xl font-bold text-slate-900 dark:text-white">Program Prioritas Bacaan (Generated by PHP)</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Daftar di bawah di-looping menggunakan <code>foreach</code> PHP dengan penomoran urut dan status dinamis.
          </p>
        </div>
        <span class="text-xs font-semibold px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg self-start sm:self-auto">
          <?php echo $totalBuku; ?> Buku Terdaftar
        </span>
      </div>

      <!-- Grid Cards Program Prioritas (Looping PHP Foreach) -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($daftarBuku as $index => $buku): ?>
          <?php
            // Task 02: Memproses data per item dalam perulangan
            $no           = $index + 1; // Loop penomoran (1, 2, 3, dst)
            $badge        = getStatusBadge($buku['status']); // Conditional badge function
            $persen       = hitungProgres($buku['halamanDibaca'], $buku['totalHalaman']); // Function persentase
            $starsHtml    = renderRatingStars($buku['rating']); // Function rating stars
          ?>
          
          <!-- Card Item Hasil Generate PHP -->
          <div class="border border-slate-100 dark:border-slate-700/70 bg-slate-50/50 dark:bg-slate-850 rounded-2xl p-5 flex flex-col justify-between hover:shadow-lg hover:border-blue-200 transition-all duration-300">
            
            <div>
              <!-- Top Row: Penomoran + Badge Status (Task 02: Conditional Badge & Loop Penomoran) -->
              <div class="flex items-center justify-between mb-3">
                <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                  #<?php echo $no; ?>
                </span>
                <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full border <?php echo $badge['class']; ?>">
                  <?php echo $badge['text']; ?>
                </span>
              </div>

              <!-- Mini Cover & Judul -->
              <div class="flex items-start gap-3.5 mb-4">
                <div class="w-14 h-20 rounded-lg bg-gradient-to-br <?php echo $buku['gradient']; ?> flex-shrink-0 p-1.5 text-white flex flex-col justify-between shadow-sm relative overflow-hidden">
                  <?php if (!empty($buku['coverUrl'])): ?>
                    <img src="<?php echo htmlspecialchars($buku['coverUrl']); ?>" class="absolute inset-0 w-full h-full object-cover rounded-lg" alt="" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent rounded-lg pointer-events-none"></div>
                  <?php endif; ?>
                  <span class="text-[8px] font-bold uppercase tracking-wider opacity-80 truncate z-10"><?php echo htmlspecialchars($buku['genre']); ?></span>
                  <span class="text-[8px] opacity-75 truncate z-10"><?php echo htmlspecialchars($buku['penulis']); ?></span>
                </div>

                <div class="min-w-0 flex-1">
                  <h3 class="font-bold text-base text-slate-900 dark:text-white leading-snug line-clamp-2">
                    <?php echo htmlspecialchars($buku['judul']); ?>
                  </h3>
                  <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Penulis: <span class="font-medium text-slate-700 dark:text-slate-300"><?php echo htmlspecialchars($buku['penulis']); ?></span>
                  </p>
                  <div class="mt-2">
                    <?php echo $starsHtml; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bottom Progress & Metrik -->
            <div class="pt-3 border-t border-slate-200/60 dark:border-slate-700/60">
              <div class="flex justify-between items-center text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">
                <span><?php echo $buku['halamanDibaca']; ?> / <?php echo $buku['totalHalaman']; ?> hlm</span>
                <span class="font-bold <?php echo $persen === 100 ? 'text-emerald-600' : 'text-blue-600'; ?>"><?php echo $persen; ?>%</span>
              </div>
              
              <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                <div class="<?php echo $persen === 100 ? 'bg-emerald-500' : 'bg-blue-600'; ?> h-2 rounded-full transition-all duration-500" style="width: <?php echo $persen; ?>%"></div>
              </div>
            </div>

          </div>
        <?php endforeach; ?>
      </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TABEL REKAPITULASI PROGRAM (PHP Procedural Loop Table) -->
    <!-- ========================================================================= -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 border border-slate-100 dark:border-slate-700/60 shadow-sm overflow-x-auto">
      <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Tabel Rekapitulasi Data (Looping Table)</h3>
      <table class="w-full text-left text-xs sm:text-sm">
        <thead>
          <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-400 uppercase text-[11px] tracking-wider">
            <th class="pb-3 font-semibold">No</th>
            <th class="pb-3 font-semibold">Judul Buku</th>
            <th class="pb-3 font-semibold">Penulis</th>
            <th class="pb-3 font-semibold">Genre</th>
            <th class="pb-3 font-semibold">Halaman</th>
            <th class="pb-3 font-semibold">Status</th>
            <th class="pb-3 font-semibold text-right">Rating</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
          <?php foreach ($daftarBuku as $i => $b): ?>
            <?php $stBadge = getStatusBadge($b['status']); ?>
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
              <td class="py-3 font-bold text-slate-500"><?php echo $i + 1; ?></td>
              <td class="py-3 font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($b['judul']); ?></td>
              <td class="py-3 text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($b['penulis']); ?></td>
              <td class="py-3"><span class="px-2 py-0.5 bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 rounded text-xs"><?php echo $b['genre']; ?></span></td>
              <td class="py-3 text-slate-500"><?php echo $b['totalHalaman']; ?> hlm</td>
              <td class="py-3"><span class="px-2 py-0.5 rounded-full border text-[11px] font-medium <?php echo $stBadge['class']; ?>"><?php echo $stBadge['text']; ?></span></td>
              <td class="py-3 text-right font-bold text-amber-500"><?php echo $b['rating'] > 0 ? $b['rating'] . ' ★' : '-'; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </main>

</body>
</html>
