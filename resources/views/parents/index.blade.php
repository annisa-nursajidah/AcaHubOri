@extends('layouts.authenticated')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Manajemen Wali Murid</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola akun portal untuk orang tua / wali siswa.</p>
    </div>
    <a href="{{ route('parents.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition-colors gap-2 shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Daftarkan Wali Murid Baru
    </a>
</div>

<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
    @if($parents->count() === 0)
        <div class="p-12 text-center flex flex-col items-center justify-center opacity-70">
            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            <h3 class="text-sm font-bold text-gray-900 mb-1">Belum Ada Wali Murid</h3>
            <p class="text-sm text-gray-500 max-w-sm">Anda belum mendaftarkan akun Portal Orang Tua. Daftarkan agar mereka bisa memantau rapor dan presensi anaknya.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50/50 text-gray-500 uppercase font-semibold text-xs border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Wali Murid</th>
                        <th class="px-6 py-4">Email Login</th>
                        <th class="px-6 py-4">Jumlah Anak</th>
                        <th class="px-6 py-4">Nama Anak</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($parents as $p)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4 font-bold text-gray-900 border-l-4 border-transparent">
                                {{ $p->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $p->email }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $p->children->count() }} Anak
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 max-w-[200px] truncate">
                                @if($p->children->count() > 0)
                                    {{ $p->children->pluck('name')->join(', ') }}
                                @else
                                    <span class="text-red-500 italic">Belum ditautkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('parents.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun wali murid berikut? Ini akan memutus koneksinya dari portal.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors ms-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($parents->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $parents->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
