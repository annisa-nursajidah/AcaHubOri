@extends('layouts.authenticated')

@section('content')
<div class="max-w-5xl mx-auto">
    
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ $exam->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $exam->subject->name }} &bull; Oleh {{ $exam->teacher->name }}</p>
        </div>

        <div class="bg-white border-2 border-brand-100 rounded-xl px-5 py-3 shadow-sm flex items-center gap-3 w-fit">
            <div class="w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center text-brand-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Sisa Waktu</p>
                <!-- Timer Placeholder -->
                <div id="countdown-timer" class="font-mono text-xl font-black text-brand-600 leading-none">
                    --:--:--
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Petunjuk -->
    @if($exam->description)
    <div class="mb-6 bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-800 flex gap-3">
        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
        <p>{{ $exam->description }}</p>
    </div>
    @endif

    <form id="exam-form" action="{{ route('student.exams.submit', [$exam, $attempt]) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <!-- Kolom Navigasi Soal (Kiri) -->
            <div class="lg:col-span-1 order-2 lg:order-1">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm sticky top-6">
                    <h3 class="font-bold text-gray-900 mb-4 text-sm">Navigasi Soal</h3>
                    
                    <div class="grid grid-cols-5 gap-2" id="question-navigator">
                        @foreach($exam->questions as $index => $question)
                            <button type="button" 
                                    onclick="document.getElementById('question-{{ $question->id }}').scrollIntoView({behavior: 'smooth', block: 'center'})"
                                    class="nav-btn w-full aspect-square flex items-center justify-center rounded-lg text-sm font-bold border relative transition-colors bg-white border-gray-200 text-gray-600 hover:border-brand-500"
                                    data-qid="{{ $question->id }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <button type="button" onclick="confirmSubmit()" class="w-full inline-flex justify-center items-center px-4 py-3 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-black transition-colors shadow-sm gap-2">
                            Selesai Ujian
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kolom Lembar Kerja Soal (Kanan) -->
            <div class="lg:col-span-3 order-1 lg:order-2 space-y-6">
                @foreach($exam->questions as $index => $question)
                <div id="question-{{ $question->id }}" class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8 shadow-sm">
                    <div class="flex gap-4 mb-6">
                        <div class="shrink-0 w-10 h-10 rounded-xl bg-gray-50 text-gray-500 font-black flex items-center justify-center border border-gray-200">
                            {{ $index + 1 }}
                        </div>
                        <div class="pt-1 flex-1">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 uppercase tracking-wider">
                                    {{ $question->type === 'multiple_choice' ? 'PG' : 'Esai' }}
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-yellow-50 text-yellow-700 border border-yellow-200 uppercase tracking-wider">
                                    {{ $question->points }} Poin
                                </span>
                            </div>
                            <div class="prose prose-sm max-w-none text-gray-900 font-medium leading-relaxed">
                                {!! nl2br(e($question->question_text)) !!}
                            </div>
                        </div>
                    </div>

                    <div class="pl-0 md:pl-14">
                        @if($question->type === 'multiple_choice')
                            <div class="space-y-3">
                                @foreach($question->options as $optIndex => $option)
                                <label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 hover:border-brand-500 hover:bg-brand-50/50 cursor-pointer transition-colors group has-[:checked]:bg-brand-50 has-[:checked]:border-brand-500">
                                    <div class="pt-0.5">
                                        <input type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="{{ $option->id }}" 
                                               class="w-4 h-4 text-brand-600 bg-white border-gray-300 focus:ring-brand-600 focus:ring-2 mt-0.5"
                                               onchange="markAnswered('{{ $question->id }}')">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded flex items-center justify-center text-xs font-bold text-gray-500 bg-gray-100 group-hover:bg-brand-100 group-hover:text-brand-700 transition-colors group-has-[:checked]:bg-brand-600 group-has-[:checked]:text-white">
                                                {{ chr(65 + $optIndex) }}
                                            </span>
                                            <span class="text-sm text-gray-700 font-medium group-has-[:checked]:text-brand-900 leading-relaxed">{{ $option->option_text }}</span>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        @else
                            <textarea name="answers[{{ $question->id }}]" rows="5" 
                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:text-gray-400"
                                      placeholder="Ketik jawaban esai Anda di sini..."
                                      oninput="markAnswered('{{ $question->id }}', this.value)"></textarea>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
        </div>
    </form>
</div>

<script>
    // System Timer
    const durationMs = {{ $timeLeftMs }};
    const endTime = new Date().getTime() + durationMs;
    const timerDisplay = document.getElementById('countdown-timer');

    const updateTimer = setInterval(function() {
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance <= 0) {
            clearInterval(updateTimer);
            timerDisplay.innerHTML = "WAKTU HABIS";
            timerDisplay.classList.replace('text-brand-600', 'text-red-500');
            // Auto submit
            document.getElementById('exam-form').submit();
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        let formattedTime = "";
        if (hours > 0) formattedTime += (hours < 10 ? "0" + hours : hours) + ":";
        formattedTime += (minutes < 10 ? "0" + minutes : minutes) + ":";
        formattedTime += (seconds < 10 ? "0" + seconds : seconds);

        timerDisplay.innerHTML = formattedTime;

        if (distance < 5 * 60 * 1000) { // Under 5 minutes left
            timerDisplay.classList.add('text-red-500', 'animate-pulse');
            timerDisplay.classList.remove('text-brand-600');
        }
    }, 1000);

    // Track Navigasi Soal yang Terjawab
    function markAnswered(questionId, value = null) {
        const btn = document.querySelector(`.nav-btn[data-qid="${questionId}"]`);
        
        let isAnswered = false;
        
        // Pengecekan apakah nilainya ada untuk text area, atau true mutlak untuk radio
        if (value !== null) {
            isAnswered = value.trim() !== '';
        } else {
            isAnswered = true; // Radio button event triggered implies it's selected
        }

        if (isAnswered) {
            btn.classList.add('bg-brand-500', 'text-white', 'border-brand-600');
            btn.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
        } else {
            btn.classList.remove('bg-brand-500', 'text-white', 'border-brand-600');
            btn.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
        }
    }

    // Konfirmasi Sumbit Manual
    function confirmSubmit() {
        // Cek pertanyaan belum terjawab (Opsional tapi bagus untuk ux)
        const unAnsweredCount = document.querySelectorAll('.nav-btn.bg-white').length;
        let msg = 'Apakah Anda yakin ingin menyelesaikan ujian dan mengirim jawaban?';
        
        if (unAnsweredCount > 0) {
            msg = `PERINGATAN: Ada ${unAnsweredCount} soal yang BELUM TERJAWAB!\n\n` + msg;
        }

        if (confirm(msg)) {
            document.getElementById('exam-form').submit();
        }
    }
    
    // Warn before navigating away
    window.onbeforeunload = function() {
        return "Jawaban mungkin tidak tersimpan jika keluar sebelum menekan Selesai Ujian.";
    };
    // Hapus peringatan saat submit form
    document.getElementById('exam-form').addEventListener('submit', function() {
        window.onbeforeunload = null;
    });
</script>
@endsection
