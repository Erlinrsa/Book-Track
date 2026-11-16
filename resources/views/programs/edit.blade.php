@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

  <!-- Breadcrumb & Back -->
  <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
    <a href="/programs" class="hover:text-blue-600 transition">&larr; Kembali ke Daftar Program</a>
    <span>/</span>
    <span class="text-slate-800">Edit Program</span>
  </div>

  <!-- Form Card Sesuai Slide 12 & 13 (04 Update / Edit Program) -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
    <div class="border-b border-slate-100 pb-5 mb-6">
      <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px] uppercase tracking-wider">
        CRUD: UPDATE
      </span>
      <h2 class="text-2xl font-bold text-slate-900 mt-2">Edit Program</h2>
      <p class="text-sm text-slate-500 mt-1">
        Mengubah data program ID <strong>#{{ $program->id }}</strong> di database melalui method <code>$program->update()</code>.
      </p>
    </div>

    <form method="POST" action="/programs/{{ $program->id }}/update" class="space-y-6">
      @csrf

      <!-- Nama Program -->
      <div>
        <label for="nama" class="block text-sm font-bold text-slate-700 mb-2">
          Nama Program <span class="text-red-500">*</span>
        </label>
        <input 
          type="text" 
          id="nama" 
          name="nama" 
          required 
          value="{{ $program->nama }}"
          class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm transition"
        />
      </div>

      <!-- Deskripsi -->
      <div>
        <label for="deskripsi" class="block text-sm font-bold text-slate-700 mb-2">
          Deskripsi Program <span class="text-red-500">*</span>
        </label>
        <textarea 
          id="deskripsi" 
          name="deskripsi" 
          rows="4" 
          required 
          class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm transition"
        >{{ $program->deskripsi }}</textarea>
      </div>

      <!-- Action Buttons Sesuai Slide 12 -->
      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <a href="/programs" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
          Batal
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
          Update Perubahan
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
