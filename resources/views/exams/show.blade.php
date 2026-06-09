@extends('layouts.authenticated')

@section('content')
<div class="mb-6 flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('exams.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ $exam->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">Builder Soal CBT &bull; Kelas {{ $exam->classroom->nama }}</p>
        </div>
    </div>
    
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg {{ $exam->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
            <span class="w-2 h-2 rounded-full {{ $exam->status === 'published' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
            {{ strtoupper($exam->status) }}
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Kolom Kiri: Info Ujian -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                Detail Pengaturan
            </h3>
            <ul class="space-y-3 text-sm">
                <li class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-500">Mata Pelajaran</span>
                    <span class="font-semibold text-gray-900">{{ $exam->subject->nama }}</span>
                </li>
                <li class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-500">Durasi Pengerjaan</span>
                    <span class="font-mono bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-bold">{{ $exam->duration_minutes }} Menit</span>
                </li>
                <li class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-500">Waktu Mulai</span>
                    <span class="font-semibold text-gray-900">{{ $exam->start_time ? $exam->start_time->format('d M Y, H:i') : 'Bebas' }}</span>
                </li>
                <li class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-500">Batas Selesai</span>
                    <span class="font-semibold text-gray-900 text-right">{{ $exam->end_time ? $exam->end_time->format('d M Y, H:i') : 'Tidak Ada' }}</span>
                </li>
                <li class="flex justify-between items-center py-2">
                    <span class="text-gray-500">Jumlah Soal</span>
                    <span class="font-black text-brand-600">{{ $exam->questions->count() }} Pertanyaan</span>
                </li>
            </ul>

            <div class="mt-5 pt-5 border-t border-gray-100 flex gap-2">
                <a href="{{ route('exams.edit', $exam) }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50 transition-colors">Edit Setelan</a>
                
                {{-- Form untuk mengganti Status (Toggle Publish/Draft) --}}
                <form action="{{ route('exams.update', $exam) }}" method="POST" class="flex-1">
                    @csrf @method('PUT')
                    <input type="hidden" name="title" value="{{ $exam->title }}">
                    <input type="hidden" name="subject_id" value="{{ $exam->subject_id }}">
                    <input type="hidden" name="classroom_id" value="{{ $exam->classroom_id }}">
                    <input type="hidden" name="duration_minutes" value="{{ $exam->duration_minutes }}">
                    <input type="hidden" name="start_time" value="{{ $exam->start_time }}">
                    <input type="hidden" name="end_time" value="{{ $exam->end_time }}">
                    
                    @if($exam->status === 'draft')
                        <input type="hidden" name="status" value="published">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white text-xs font-semibold rounded-xl hover:bg-green-700 transition-colors shadow-sm" onclick="return confirm('Publikasikan ujian ini agar dapat dilihat siswa?');">Publikasikan</button>
                    @else
                        <input type="hidden" name="status" value="draft">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-yellow-500 text-white text-xs font-semibold rounded-xl hover:bg-yellow-600 transition-colors shadow-sm">Tarik ke Draft</button>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Builder Soal -->
    <div class="lg:col-span-2 space-y-4">
        
        <div x-data="{ 
            questionType: null, // 'multiple_choice' or 'essay'
            openModal(type) { this.questionType = type; },
            closeModal() { this.questionType = null; }
        }">
            <!-- Tombol Pemicu -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button @click="openModal('multiple_choice')" class="flex-1 bg-white border-2 border-dashed border-gray-200 hover:border-brand-500 hover:bg-brand-50 text-gray-600 hover:text-brand-600 rounded-2xl p-4 flex flex-col items-center justify-center transition-all group">
                    <div class="w-10 h-10 rounded-full bg-gray-100 group-hover:bg-brand-100 flex items-center justify-center mb-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </div>
                    <span class="font-bold text-sm">Pilihan Ganda</span>
                </button>
                <button @click="openModal('essay')" class="flex-1 bg-white border-2 border-dashed border-gray-200 hover:border-accent-500 hover:bg-accent-50 text-gray-600 hover:text-accent-600 rounded-2xl p-4 flex flex-col items-center justify-center transition-all group">
                    <div class="w-10 h-10 rounded-full bg-gray-100 group-hover:bg-accent-100 flex items-center justify-center mb-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </div>
                    <span class="font-bold text-sm">Soal Esai</span>
                </button>
            </div>

            <!-- Modal Form Tambah Soal -->
            <div x-show="questionType !== null" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto w-full h-full bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
                <div @click.away="closeModal" class="bg-white w-full max-w-2xl rounded-2xl shadow-xl border border-gray-100 overflow-hidden transform transition-all">
                    
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-900" x-text="questionType === 'multiple_choice' ? 'Tambah Pilihan Ganda' : 'Tambah Soal Esai'"></h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>

                    <form action="{{ route('exams.questions.store', $exam) }}" method="POST" class="p-6 space-y-6" data-novalidate>
                        @csrf
                        <input type="hidden" name="type" x-bind:value="questionType">

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pertanyaan <span class="text-red-500">*</span></label>
                            <textarea name="question_text" rows="3" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all"></textarea>
                        </div>

                         <!-- Pilihan Ganda Builder -->
                         <template x-if="questionType === 'multiple_choice'">
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Opsi Jawaban & Kunci <span class="text-red-500">*</span></label>
                                <!-- Kita bisa menggunakan perulangan 4/5 opsi A-E statis menggunakan Alpine/Blade -> di loop 4x -->
                                @foreach(range(0, 3) as $i)
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="correct_option" value="{{ $i }}" {{ $i == 0 ? 'required' : '' }} class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500">
                                    <div class="w-6 h-6 rounded flex-shrink-0 bg-gray-100 flex items-center justify-center text-xs font-bold">{{ chr(65 + $i) }}</div>
                                    <input type="text" name="options[{{ $i }}]" placeholder="Teks opsi {{ chr(65 + $i) }}..." class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500" {{ $i < 2 ? 'required' : '' }}>
                                </div>
                                @endforeach
                                <p class="text-xs text-gray-500 mt-2">Pilih radio button di sebelah kiri untuk menandai mana opsi yang benar. Kosongkan isian C/D jika tidak digunakan.</p>
                            </div>
                        </template>

                        <div class="w-32">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bobot Nilai <span class="text-red-500">*</span></label>
                            <input type="number" name="points" value="10" min="1" max="100" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 transition-all text-center font-mono">
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" @click="closeModal" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-xl hover:bg-brand-700 transition-colors">Simpan Soal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($exam->questions->count() == 0)
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-10 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-gray-100">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 mb-1">Bank Soal Kosong</h3>
                <p class="text-sm text-gray-500">Klik tombol di atas untuk mulai memuat pertanyaan untuk Ujian CBT ini.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($exam->questions as $index => $question)
                    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm relative group">
                        <div class="absolute right-4 top-4 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <form action="{{ route('exams.questions.destroy', [$exam, $question]) }}" method="POST" onsubmit="return confirm('Yakin hapus soal ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded" title="Hapus Soal"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg></button>
                            </form>
                        </div>
                        
                        <div class="flex gap-4">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-brand-50 text-brand-600 font-black flex items-center justify-center text-sm shadow-inner">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $question->type === 'multiple_choice' ? 'bg-blue-50 text-blue-600' : 'bg-accent-50 text-accent-600' }} uppercase tracking-wider">
                                        {{ $question->type === 'multiple_choice' ? 'Pilihan Ganda' : 'Esai' }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 uppercase tracking-wider">{{ $question->points }} Poin</span>
                                </div>
                                <p class="text-gray-900 font-medium mb-3">{!! nl2br(e($question->question_text)) !!}</p>
                                
                                @if($question->type === 'multiple_choice')
                                    <ul class="space-y-2 mt-4 ml-1">
                                        @foreach($question->options as $optIndex => $option)
                                            <li class="flex items-center gap-3 p-2 rounded-lg {{ $option->is_correct ? 'bg-green-50/50 border border-green-100' : '' }}">
                                                <div class="w-6 h-6 rounded border font-bold text-[10px] flex items-center justify-center {{ $option->is_correct ? 'bg-green-500 border-green-600 text-white' : 'bg-gray-50 border-gray-200 text-gray-500' }}">
                                                    {{ chr(65 + $optIndex) }}
                                                </div>
                                                <span class="text-sm {{ $option->is_correct ? 'text-green-800 font-medium' : 'text-gray-600' }}">{{ $option->option_text }}</span>
                                                @if($option->is_correct)
                                                    <svg class="w-4 h-4 text-green-500 ml-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
