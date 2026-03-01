@extends('layouts.authenticated')
@section('content')
@php $title = 'Dashboard'; @endphp

{{-- Greeting --}}
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-gray-900">Selamat datang, {{ $user->name }}! 👋</h1>
    <p class="text-sm text-gray-500 mt-0.5">
        @if($user->isAdmin()) Panel administrasi AcaHub
        @elseif($user->isTeacher()) Ringkasan mengajar Anda
        @else Ringkasan akademik Anda
        @endif
    </p>
</div>

{{-- Stat cards --}}
        </div>

        {{-- Widget Link Pendaftaran (PPDB) --}}
        <div class="bg-gradient-to-br from-brand-600 to-accent-600 rounded-2xl p-5 border border-brand-500 shadow-lg text-white relative overflow-hidden flex flex-col justify-between group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold leading-tight">Link PPDB Publik</h3>
                        <p class="text-[11px] text-white/80">Bagikan untuk pendaftaran</p>
                    </div>
                </div>
                
                <div class="mt-auto">
                    <div class="bg-black/20 p-2.5 rounded-lg border border-white/10 flex items-center justify-between gap-2 backdrop-blur-md">
                        <span class="text-xs font-mono truncate cursor-text" id="ppdbLink">{{ url('/daftar/' . $user->school_id) }}</span>
                        <button onclick="navigator.clipboard.writeText(document.getElementById('ppdbLink').innerText); alert('Link berhasil disalin!')" 
                                class="shrink-0 p-1.5 rounded-md hover:bg-white/20 transition-colors tooltip" title="Salin Link">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"/></svg>
                        </button>
                    </div>
                    
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-xs text-brand-100 font-medium">Kode Undangan:</span>
                        <div class="flex items-center gap-2">
                            @if(\App\Models\School::find($user->school_id)->invite_code)
                                <span class="bg-white text-accent-600 font-bold px-2 py-0.5 rounded text-xs tracking-wider" id="inviteCode">
                                    {{ \App\Models\School::find($user->school_id)->invite_code }}
                                </span>
                            @else
                                <span class="text-xs italic text-brand-200">Belum Ada</span>
                            @endif
                            <!-- Nanti ditambahkan form action post update_invite_code -->
                            <form action="{{ route('schools.regenerate-invite', $user->school_id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-white hover:text-white/70" title="Ubah / Generate Kode">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($user->isTeacher())
        <x-dashboard-stat icon="subjects" label="Mapel Saya" :value="$mySubjects ?? 0" color="brand" />
        <x-dashboard-stat icon="grades" label="Nilai Diberikan" :value="$myGradesGiven ?? 0" color="accent" />
        <x-dashboard-stat icon="students" label="Total Siswa" :value="$totalStudents" color="green" />
        <x-dashboard-stat icon="subjects" label="Total Mapel" :value="$totalSubjects" color="blue" />
    @else
        <x-dashboard-stat icon="grades" label="Nilai Saya" :value="$myGradeCount ?? 0" color="brand" />
        <x-dashboard-stat icon="grades" label="Rata-rata" :value="number_format($myAverage ?? 0, 1)" color="{{ ($myAverage ?? 0) >= 75 ? 'green' : 'accent' }}" />
        <x-dashboard-stat icon="grades" label="Tertinggi" :value="number_format($myHighest ?? 0, 1)" color="green" />
        <x-dashboard-stat icon="grades" label="Terendah" :value="number_format($myLowest ?? 0, 1)" color="{{ ($myLowest ?? 0) >= 75 ? 'green' : 'accent' }}" />
    @endif
</div>

{{-- Charts row --}}
@if(!$user->isAdmin())
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    {{-- Grade Distribution --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-bold text-gray-700 mb-4">Distribusi Nilai</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="gradeDistChart"></canvas>
        </div>
    </div>

    {{-- Subject Averages --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-bold text-gray-700 mb-4">Rata-rata per Mata Pelajaran</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="subjectAvgChart"></canvas>
        </div>
    </div>
</div>
@endif

{{-- Quick actions --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h3 class="text-sm font-bold text-gray-700 mb-4">Aksi Cepat</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('grades.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:border-brand-300 transition">
            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75Z"/></svg>
            Lihat Nilai
        </a>
        <a href="{{ route('subjects.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:border-brand-300 transition">
            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25"/></svg>
            Mata Pelajaran
        </a>
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:border-brand-300 transition">
            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5"/></svg>
            Lihat Rapor
        </a>
        <a href="{{ route('profile.show') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:border-brand-300 transition">
            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0"/></svg>
            Profil Saya
        </a>
        @if($user->isAdmin())
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:border-brand-300 transition">
                <svg class="w-4 h-4 text-accent-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372"/></svg>
                Kelola Users
            </a>
            <a href="{{ route('grades.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Input Nilai Baru
            </a>
        @elseif($user->isTeacher())
            <a href="{{ route('grades.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 shadow-lg shadow-accent-500/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Input Nilai
            </a>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
// Grade distribution doughnut
const distCtx = document.getElementById('gradeDistChart').getContext('2d');
new Chart(distCtx, {
    type: 'doughnut',
    data: {
        labels: ['A (≥90)', 'B (75-89)', 'C (60-74)', 'D (50-59)', 'E (<50)'],
        datasets: [{
            data: @json(array_values($gradeDistribution)),
            backgroundColor: ['#10b981','#0891b2','#f59e0b','#f97316','#ef4444'],
            borderWidth: 0,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyleWidth: 8, font: { size: 11, family: 'Inter' } } }
        }
    }
});

// Subject averages bar chart
const avgCtx = document.getElementById('subjectAvgChart').getContext('2d');
const subjectData = @json($subjectAverages);
new Chart(avgCtx, {
    type: 'bar',
    data: {
        labels: subjectData.map(s => s.name),
        datasets: [{
            label: 'Rata-rata',
            data: subjectData.map(s => s.avg),
            backgroundColor: subjectData.map(s => s.avg >= 75 ? '#0891b2' : '#f97316'),
            borderRadius: 8,
            borderSkipped: false,
            barThickness: 32,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true, max: 100, ticks: { font: { size: 11, family: 'Inter' } }, grid: { color: '#f3f4f6' } },
            x: { ticks: { font: { size: 10, family: 'Inter' } }, grid: { display: false } }
        },
        plugins: {
            legend: { display: false },
            annotation: {
                annotations: { kkm: { type: 'line', yMin: 75, yMax: 75, borderColor: '#ef4444', borderWidth: 1, borderDash: [4,4] } }
            }
        }
    }
});
</script>
@endpush
@endsection
