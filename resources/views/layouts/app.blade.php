<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BookTrack & PresidentKu - Laravel Data Management (Sprint 07)</title>
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#eff6ff',
              100: '#e0edff',
              500: '#2563eb',
              600: '#1d6cdb',
              700: '#1d4ed8',
              800: '#1e40af',
              900: '#1e3a8a',
            }
          }
        }
      }
    }
  </script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen antialiased flex flex-col">

  <!-- Top Navbar -->
  <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="/programs" class="flex items-center gap-2">
          <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-red-600 to-amber-500 flex items-center justify-center text-white font-black text-lg shadow-sm">
            PK
          </span>
          <div>
            <h1 class="font-bold text-lg text-slate-900 tracking-tight leading-none">PresidentKu <span class="text-blue-600">· BookTrack</span></h1>
            <p class="text-[10px] text-slate-400 mt-0.5 font-medium">Laravel Framework · Eloquent ORM & CRUD</p>
          </div>
        </a>
        <span class="ml-2 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200">
          Sprint 07 (Week 9)
        </span>
      </div>

      <!-- Navigation & Previous Weeks Link -->
      <nav class="flex items-center gap-2">
        <a href="/programs" class="px-3.5 py-2 rounded-lg text-sm font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
          Program Management
        </a>
        <a href="/test-eloquent" class="px-3 py-2 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition" target="_blank">
          Test Eloquent API ↗
        </a>
        <a href="/index_week7_mvc.php" class="px-3 py-2 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">
          Week 7 (MVC)
        </a>
        <a href="/index.html" class="px-3 py-2 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">
          Week 4 (Client API)
        </a>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    @yield('content')
  </main>

  <!-- Footer -->
  <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
    <p>Praktikum Pemrograman Web · Pertemuan 9: <strong>Laravel + CRUD + Database</strong> (Sprint 07)</p>
    <p class="mt-1 text-[11px] text-slate-400">Model · Eloquent ORM · Migration · SQLite/MySQL Database</p>
  </footer>

</body>
</html>
