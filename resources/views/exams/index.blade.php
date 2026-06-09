@extends('layouts.authenticated')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">CBT: Ujian Online</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola ujian dan kuis untuk siswa Anda.</p>
    </div>
    <a href="{{ route('exams.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition-colors gap-2 shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Buat Ujian Baru
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Mata Pelajaran & Judul</th>
                    <th class="py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Kelas</th>
                    <th class="py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Durasi</th>
                    <th class="py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="py-4 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($exams as $exam)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-900">{{ $exam->title }}</div>
                            <div class="text-xs text-brand-600 font-medium mt-0.5">{{ $exam->subject->nama }}</div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold bg-gray-100 text-gray-700 rounded-lg">
                                {{ $exam->classroom->nama }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600">
                            {{ $exam->duration_minutes }} Menit
                        </td>
                        <td class="py-4 px-6">
                            @if($exam->status === 'draft')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-lg"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Draft</span>
                            @elseif($exam->status === 'published')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium bg-green-50 text-green-700 rounded-lg"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded-lg"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Selesai</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('exams.show', $exam) }}" class="p-2 text-brand-600 hover:bg-brand-50 rounded-lg transition-colors tooltip" title="Kelola Soal">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M19.5 13.5v7.5m-3-3h6"/></svg>
                                </a>
                                <a href="{{ route('exams.edit', $exam) }}" class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors tooltip" title="Edit Pengaturan Ujian">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                                </a>
                                <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('Hapus ujian ini berserta seluruh soal di dalamnya?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors tooltip" title="Hapus Ujian">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 px-6 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900">Belum ada ujian</h3>
                            <p class="text-sm text-gray-500 mt-1 mb-4">Mulai laksanakan CBT dengan membuat ujian pertama Anda.</p>
                            <a href="{{ route('exams.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-brand-50 text-brand-600 text-sm font-semibold rounded-lg hover:bg-brand-100 transition-colors">
                                Buat Ujian
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($exams->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $exams->links() }}
        </div>
    @endif
</div>
@endsection
