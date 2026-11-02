<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($appName); ?> - <?= htmlspecialchars($pageTitle); ?></title>
  
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
  <!-- SIDEBAR NAVIGATION (VIEW: Menampilkan Data User dari Controller) -->
  <!-- ========================================================================= -->
  <aside id="sidebar" class="w-64 bg-white dark:bg-slate-900 border-r border-slate-100 dark:border-slate-800 flex flex-col justify-between p-6 fixed lg:sticky top-0 h-screen z-40">
    <div>
      <!-- Brand Logo -->
      <div class="mb-8 pt-2 flex items-center justify-between">
        <a href="index.php" class="font-serif font-black text-2xl text-blue-950 dark:text-blue-400 tracking-tight">
          <?= htmlspecialchars($appName); ?>
        </a>
        <span class="text-[10px] bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300 font-bold px-2.5 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
          Sprint 06 MVC
        </span>
      </div>

      <!-- User Profile Card (Data dari Model User yang dikirim Controller) -->
      <div class="mb-6 p-3.5 bg-slate-50 dark:bg-slate-800/70 border border-slate-100 dark:border-slate-700/60 rounded-xl flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-600 flex items-center justify-center text-white font-bold text-sm shadow-sm flex-shrink-0">
          <?= htmlspecialchars($user->getInitials()); ?>
        </div>
        <div class="min-w-0">
          <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
            <?= htmlspecialchars($user->getName()); ?>
          </p>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
            <?= htmlspecialchars($user->getRole()); ?>
          </p>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="space-y-1.5">
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 transition">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
          </svg>
          <span>Aplikasi MVC (W7)</span>
        </a>
        <a href="index.html" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span>Client-Side API (W4)</span>
        </a>
      </nav>
    </div>

    <!-- Info Alur MVC -->
    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400">
      <p class="font-bold text-slate-700 dark:text-slate-300">Struktur MVC Aktif:</p>
      <p class="text-[10px] text-slate-400 mt-0.5">Model &rarr; Controller &rarr; View</p>
    </div>
  </aside>

  <!-- ========================================================================= -->
  <!-- MAIN CONTENT AREA -->
  <!-- ========================================================================= -->
  <main class="flex-1 p-6 sm:p-10 max-w-7xl mx-auto w-full">
    
    <!-- Header Salam Dinamis (View menerima data siap tampil dari Controller) -->
    <div class="mb-8">
      <div class="inline-flex items-center gap-2 text-xs font-semibold tracking-wider uppercase text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/60 dark:border-emerald-800 px-3.5 py-1.5 rounded-full">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span>Arsitektur MVC (Model - View - Controller) · Week 7</span>
      </div>
      
      <h1 class="text-3xl sm:text-4xl font-serif font-bold text-slate-900 dark:text-white tracking-tight mt-3">
        Selamat <?= $user->getGreeting(); ?>, <?= htmlspecialchars($user->getName()); ?>.
      </h1>
      <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm sm:text-base">
        Profil: <strong><?= htmlspecialchars($user->getProfile()); ?></strong> — Halaman ini dihasilkan melalui alur: 
        <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-xs rounded text-emerald-600 font-semibold">Request &rarr; Controller &rarr; Model &rarr; View</code>
      </p>
    </div>

    <!-- ========================================================================= -->
    <!-- 4 KARTU STATISTIK (Data diolah Controller dari Model) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8">
      
      <!-- Card 1: Total Buku -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white"><?= $stats['totalBooks']; ?></p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Total Koleksi Model</p>
      </div>

      <!-- Card 2: Halaman Terbaca -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white"><?= number_format($stats['totalPagesRead'], 0, ',', '.'); ?></p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Halaman Terbaca</p>
      </div>

      <!-- Card 3: Target Selesai -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-emerald-600 dark:text-emerald-400">
          <?= $stats['finishedCount']; ?> / <?= $user->getYearlyTarget(); ?>
        </p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">
          Target Selesai (<?= $stats['targetPercentage']; ?>%)
        </p>
      </div>

      <!-- Card 4: Rata-Rata Rating -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-amber-500"><?= $stats['averageRating']; ?> ⭐</p>
        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Rata-rata Rating</p>
      </div>

    </div>

    <!-- ========================================================================= -->
    <!-- REQUEST HANDLING: FILTER TABS (Menunjukkan Controller menerima request) -->
    <!-- ========================================================================= -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm">
      <div class="flex items-center gap-2">
        <span class="text-xs font-bold uppercase text-slate-400 tracking-wider mr-2">Filter Request:</span>
        <a href="index.php?status=all" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $statusFilter === 'all' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200'; ?>">
          Semua (<?= $stats['totalBooks']; ?>)
        </a>
        <a href="index.php?status=Sedang dibaca" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $statusFilter === 'Sedang dibaca' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200'; ?>">
          Sedang Dibaca
        </a>
        <a href="index.php?status=Selesai" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $statusFilter === 'Selesai' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200'; ?>">
          Selesai (<?= $stats['finishedCount']; ?>)
        </a>
        <a href="index.php?status=Ingin dibaca" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $statusFilter === 'Ingin dibaca' ? 'bg-slate-700 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200'; ?>">
          Ingin Dibaca
        </a>
      </div>
      <span class="text-xs text-slate-400">
        Menampilkan <strong><?= count($books); ?></strong> data buku
      </span>
    </div>

    <!-- ========================================================================= -->
    <!-- DYNAMIC GRID CARDS (VIEW ITERATION: Data dikirim dari Controller) -->
    <!-- ========================================================================= -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 border border-slate-100 dark:border-slate-700/60 shadow-sm mb-10">
      
      <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-100 dark:border-slate-700 gap-2">
        <div>
          <h2 class="text-xl font-bold text-slate-900 dark:text-white">Program Prioritas Bacaan (Rendered by View)</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            View menerima data <code>$books</code> dari Controller tanpa mengetahui proses query/logika di balik layar.
          </p>
        </div>
        <span class="text-xs font-semibold px-3 py-1 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-lg self-start sm:self-auto border border-emerald-200/50">
          Status: <?= htmlspecialchars(ucfirst($statusFilter)); ?>
        </span>
      </div>

      <!-- Grid Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($books)): ?>
          <div class="col-span-3 text-center py-12 text-slate-400 text-sm">
            Tidak ada buku dengan status ini.
          </div>
        <?php else: ?>
          <?php foreach ($books as $index => $book): ?>
            <?php
              $no        = $index + 1;
              $badge     = $book->getStatusBadge();
              $progress  = $book->getProgressPercentage();
              $starsHtml = $book->renderRatingStars();
            ?>
            
            <!-- Card Item -->
            <div class="border border-slate-100 dark:border-slate-700/70 bg-slate-50/50 dark:bg-slate-850 rounded-2xl p-5 flex flex-col justify-between hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
              
              <div>
                <!-- Top Row: Penomoran + Badge Status -->
                <div class="flex items-center justify-between mb-3">
                  <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
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
                  <span class="font-bold <?= $progress === 100 ? 'text-emerald-600' : 'text-blue-600'; ?>"><?= $progress; ?>%</span>
                </div>
                
                <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                  <div class="<?= $progress === 100 ? 'bg-emerald-500' : 'bg-blue-600'; ?> h-2 rounded-full transition-all duration-500" style="width: <?= $progress; ?>%"></div>
                </div>
              </div>

            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TABEL REKAPITULASI DATA MVC -->
    <!-- ========================================================================= -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 border border-slate-100 dark:border-slate-700/60 shadow-sm overflow-x-auto">
      <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Tabel Rekapitulasi Data (Alur MVC)</h3>
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
          <?php foreach ($books as $i => $b): ?>
            <?php $stBadge = $b->getStatusBadge(); ?>
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
              <td class="py-3 font-bold text-slate-500"><?= $i + 1; ?></td>
              <td class="py-3 font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($b->getTitle()); ?></td>
              <td class="py-3 text-slate-500 dark:text-slate-400"><?= htmlspecialchars($b->getAuthor()); ?></td>
              <td class="py-3"><span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 rounded text-xs"><?= $b->getGenre(); ?></span></td>
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
