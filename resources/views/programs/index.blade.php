@extends('layouts.app')

@section('content')
<div class="space-y-6">

  <!-- Header Section Sesuai Slide 12 & 13 -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200 gap-4">
    <div>
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold mb-2">
        <span>● Database Connected</span>
        <span class="text-slate-400">|</span>
        <span>Table: programs</span>
      </div>
      <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Program PresidentKu</h2>
      <p class="text-sm text-slate-500 mt-1">
        Pengelolaan data program prioritas berbasis <strong>Laravel Eloquent ORM & Database</strong> (Sprint 07 / Week 9).
      </p>
    </div>

    <!-- Tombol + Tambah Program Sesuai Slide 12 -->
    <div>
      <a href="/programs/create" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        <span>+ Tambah Program</span>
      </a>
    </div>
  </div>

  <!-- Banner Info Alur CRUD -->
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-4 text-xs text-blue-900 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-2">
      <span class="px-2 py-0.5 rounded bg-blue-600 text-white font-bold text-[10px]">Alur CRUD</span>
      <span><strong>READ:</strong> Data diambil langsung dari tabel database melalui <code>Program::all()</code> tanpa hard-code di HTML.</span>
    </div>
    <span class="font-bold text-blue-700">Total Program: {{ count($programs) }}</span>
  </div>

  <!-- Tabel Data Program Sesuai Slide 12 & 13 -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] tracking-wider font-semibold">
          <tr>
            <th class="py-4 px-6 w-16">No</th>
            <th class="py-4 px-6 w-1/4">Nama Program</th>
            <th class="py-4 px-6">Deskripsi</th>
            <th class="py-4 px-6 w-44 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @if(count($programs) === 0)
            <tr>
              <td colspan="4" class="py-12 text-center text-slate-400">
                Belum ada data program. Klik <strong>+ Tambah Program</strong> untuk menambahkan.
              </td>
            </tr>
          @else
            @foreach($programs as $index => $program)
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-4 px-6 font-bold text-slate-400">
                  {{ $index + 1 }}
                </td>
                <td class="py-4 px-6 font-bold text-slate-900">
                  <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span>{{ $program->nama }}</span>
                  </div>
                </td>
                <td class="py-4 px-6 text-slate-600 leading-relaxed">
                  {{ $program->deskripsi }}
                </td>
                <td class="py-4 px-6 text-right">
                  <div class="inline-flex items-center gap-2">
                    <!-- Tombol Edit Sesuai Slide 12 -->
                    <a href="/programs/{{ $program->id }}/edit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition">
                      Edit
                    </a>
                    
                    <!-- Tombol Hapus (Trigger Modal) Sesuai Slide 12 & 13 -->
                    <button type="button" onclick="openDeleteModal('{{ $program->id }}', '{{ htmlspecialchars($program->nama, ENT_QUOTES) }}')" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition">
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
          @endif
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Modal Konfirmasi Hapus Sesuai Slide 13 (05 Delete / Hapus Program) -->
<div id="deleteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
    <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-4">
      <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
      </svg>
    </div>
    
    <h3 class="text-lg font-bold text-slate-900">Hapus Program</h3>
    <p class="text-sm text-slate-500 mt-2">
      Yakin ingin menghapus program <strong id="deleteProgramName" class="text-slate-900"></strong> dari database?
    </p>

    <form id="deleteForm" method="POST" action="" class="mt-6 flex items-center justify-end gap-3">
      @csrf
      <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
        Batal
      </button>
      <button type="submit" class="px-5 py-2 rounded-xl text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm">
        Hapus
      </button>
    </form>
  </div>
</div>

<script>
  function openDeleteModal(id, name) {
    document.getElementById('deleteProgramName').textContent = name;
    document.getElementById('deleteForm').action = '/programs/' + id + '/delete';
    document.getElementById('deleteModal').classList.remove('hidden');
  }

  function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
  }
</script>
@endsection
