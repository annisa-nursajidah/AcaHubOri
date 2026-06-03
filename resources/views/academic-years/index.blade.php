@extends('layouts.authenticated')
@section('content')
@php $title = 'Tahun Ajaran'; @endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Tahun Ajaran</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola periode akademik sekolah</p>
    </div>
    <div class="flex items-center gap-3">
        {{-- Tombol Mulai Semester Baru (school_admin & admin) --}}
        @if(auth()->user()->isSchoolAdmin() || auth()->user()->isAdmin())
        <button onclick="document.getElementById('modalSemesterBaru').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-amber-500/25 hover:bg-amber-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
            Mulai Semester Baru
        </button>
        @endif

        @if(auth()->user()->isAdmin())
        <a href="{{ route('academic-years.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25 hover:bg-brand-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Manual
        </a>
        @endif
    </div>
</div>

{{-- Info Semester Aktif --}}
@php $activeYear = $years->firstWhere('is_active', true); @endphp
@if($activeYear)
<div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 flex items-center gap-3">
    <span class="w-3 h-3 rounded-full bg-green-500 flex-shrink-0 animate-pulse"></span>
    <div>
        <p class="text-sm font-semibold text-green-800">Semester Aktif Sekarang</p>
        <p class="text-sm text-green-700">
            {{ $activeYear->tahun }} — {{ $activeYear->semester }}
            @if($activeYear->tanggal_mulai)
                · {{ $activeYear->tanggal_mulai->format('d M Y') }} s/d {{ $activeYear->tanggal_selesai?->format('d M Y') ?? '—' }}
            @endif
        </p>
    </div>
</div>
@else
<div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 flex items-center gap-3">
    <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
    <p class="text-sm text-amber-800 font-medium">Belum ada semester aktif. Klik "Mulai Semester Baru" untuk memulai.</p>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50/80">
            <tr>
                <th class="text-left px-6 py-4 font-semibold text-gray-600">Tahun / Semester</th>
                <th class="text-left px-6 py-4 font-semibold text-gray-600">Tanggal Mulai</th>
                <th class="text-left px-6 py-4 font-semibold text-gray-600">Tanggal Selesai</th>
                <th class="text-center px-6 py-4 font-semibold text-gray-600">Status</th>
                <th class="text-right px-6 py-4 font-semibold text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($years as $year)
            <tr class="hover:bg-gray-50/50 transition">
                <td class="px-6 py-4 font-medium text-gray-800">
                    {{ $year->tahun }} — {{ $year->semester }}
                </td>
                <td class="px-6 py-4 text-gray-600">
                    {{ $year->tanggal_mulai?->format('d M Y') ?? '—' }}
                </td>
                <td class="px-6 py-4 text-gray-600">
                    {{ $year->tanggal_selesai?->format('d M Y') ?? '—' }}
                </td>
                <td class="px-6 py-4 text-center">
                    @if($year->is_active)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Tidak Aktif</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->isSchoolAdmin())
                            @unless($year->is_active)
                            <form method="POST" action="{{ route('academic-years.activate', $year) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition">Aktifkan</button>
                            </form>
                            @endunless
                            <a href="{{ route('academic-years.edit', $year) }}" class="px-3 py-1.5 text-xs font-medium bg-brand-50 text-brand-700 rounded-lg hover:bg-brand-100 transition">Edit</a>
                            <form method="POST" action="{{ route('academic-years.destroy', $year) }}" onsubmit="return confirm('Hapus tahun ajaran ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">Hapus</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-400">Belum ada data tahun ajaran.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $years->links() }}</div>

{{-- ═══ MODAL: Mulai Semester Baru ══════════════════════════════════════ --}}
<div id="modalSemesterBaru" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalSemesterBaru').classList.add('hidden')"></div>

    {{-- Modal Card --}}
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Mulai Semester Baru</h2>
                <p class="text-sm text-gray-500 mt-0.5">Semester aktif akan ditutup dan siswa otomatis didaftarkan ulang</p>
            </div>
            <button onclick="document.getElementById('modalSemesterBaru').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('academic-years.start-new-semester') }}">
            @csrf
            <div class="px-6 py-5 space-y-4">

                {{-- Info semester saat ini --}}
                @if($activeYear)
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-800">
                    <strong>Akan menutup:</strong> {{ $activeYear->tahun }} — {{ $activeYear->semester }}
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tahun Ajaran Baru <span class="text-red-500">*</span></label>
                        <input type="text" name="tahun" placeholder="2025/2026"
                            value="{{ $activeYear ? $activeYear->tahun : '' }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition" required>
                        <p class="text-[11px] text-gray-400 mt-1">Contoh: 2025/2026</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Semester <span class="text-red-500">*</span></label>
                        <select name="semester" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition" required>
                            @if($activeYear && $activeYear->semester === 'Ganjil')
                                <option value="Genap" selected>Genap</option>
                                <option value="Ganjil">Ganjil</option>
                            @else
                                <option value="Ganjil" selected>Ganjil</option>
                                <option value="Genap">Genap</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition">
                    </div>
                </div>

                {{-- Info proses --}}
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-800 space-y-1.5">
                    <p class="font-semibold">Yang akan terjadi secara otomatis:</p>
                    <ul class="space-y-1 text-blue-700">
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Semester aktif saat ini ditutup</li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            @if($activeYear && $activeYear->semester === 'Ganjil')
                                Siswa tetap di kelas yang sama (Ganjil → Genap)
                            @else
                                Siswa naik satu tingkat kelas (Genap → Ganjil tahun baru)
                            @endif
                        </li>
                        <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Notifikasi dikirim ke seluruh pengguna</li>
                    </ul>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalSemesterBaru').classList.add('hidden')"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 text-white rounded-xl font-semibold text-sm hover:bg-amber-600 transition shadow-lg shadow-amber-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                    Mulai Semester Baru
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
