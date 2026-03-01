<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor — {{ $student->user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 3px solid #0891b2; }
        .header h1 { font-size: 20px; color: #0891b2; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #6b7280; }
        .info-grid { display: table; width: 100%; margin-bottom: 20px; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 130px; padding: 3px 8px 3px 0; font-weight: 600; color: #374151; }
        .info-value { display: table-cell; padding: 3px 0; color: #4b5563; }
        table.grades { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.grades th { background: #0891b2; color: white; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        table.grades th:first-child { border-radius: 6px 0 0 0; }
        table.grades th:last-child { border-radius: 0 6px 0 0; }
        table.grades td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        table.grades tr:nth-child(even) td { background: #f9fafb; }
        .status-tuntas { color: #15803d; font-weight: 600; }
        .status-belum { color: #dc2626; font-weight: 600; }
        .summary { background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 8px; padding: 16px; margin-top: 16px; }
        .summary h3 { color: #0891b2; margin-bottom: 8px; font-size: 13px; }
        .footer { margin-top: 40px; text-align: center; color: #9ca3af; font-size: 9px; border-top: 1px solid #e5e7eb; padding-top: 12px; }
        .signature-area { margin-top: 40px; display: table; width: 100%; }
        .signature-box { display: table-cell; width: 50%; text-align: center; }
        .signature-line { margin-top: 60px; border-top: 1px solid #374151; width: 160px; display: inline-block; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPOR AKADEMIK</h1>
        <p>AcaHub — Sistem Manajemen Akademik</p>
    </div>

    <div class="info-grid">
        <div class="info-row"><div class="info-label">Nama Siswa</div><div class="info-value">: {{ $student->user->name }}</div></div>
        <div class="info-row"><div class="info-label">NIS</div><div class="info-value">: {{ $student->nis ?? '—' }}</div></div>
        <div class="info-row"><div class="info-label">Kelas</div><div class="info-value">: {{ $student->kelas ?? '—' }}</div></div>
        <div class="info-row"><div class="info-label">Semester</div><div class="info-value">: {{ $semester }}</div></div>
        <div class="info-row"><div class="info-label">Tahun Ajaran</div><div class="info-value">: {{ $tahunAjaran }}</div></div>
    </div>

    <table class="grades">
        <thead>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Guru</th>
                <th>Tugas</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Praktik</th>
                <th>Rata-rata</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjectGrades as $i => $sg)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $sg->subject->nama }}</td>
                <td>{{ $sg->teacher }}</td>
                <td>{{ $sg->tugas !== null ? number_format($sg->tugas, 1) : '—' }}</td>
                <td>{{ $sg->uts !== null ? number_format($sg->uts, 1) : '—' }}</td>
                <td>{{ $sg->uas !== null ? number_format($sg->uas, 1) : '—' }}</td>
                <td>{{ $sg->praktik !== null ? number_format($sg->praktik, 1) : '—' }}</td>
                <td><strong>{{ number_format($sg->average, 1) }}</strong></td>
                <td class="{{ $sg->status === 'Tuntas' ? 'status-tuntas' : 'status-belum' }}">{{ $sg->status }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center; padding:20px; color:#9ca3af;">Belum ada data nilai.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($subjectGrades->count() > 0)
    <div class="summary">
        <h3>Ringkasan</h3>
        <div class="info-grid">
            <div class="info-row"><div class="info-label">Rata-rata Keseluruhan</div><div class="info-value">: <strong>{{ number_format($overallAvg, 1) }}</strong></div></div>
            <div class="info-row"><div class="info-label">Jumlah Mapel</div><div class="info-value">: {{ $subjectGrades->count() }}</div></div>
            <div class="info-row"><div class="info-label">Tuntas</div><div class="info-value">: {{ $subjectGrades->where('status', 'Tuntas')->count() }} mapel</div></div>
            <div class="info-row"><div class="info-label">Belum Tuntas</div><div class="info-value">: {{ $subjectGrades->where('status', 'Belum Tuntas')->count() }} mapel</div></div>
        </div>
    </div>
    @endif

    <div class="signature-area">
        <div class="signature-box">
            <p>Wali Kelas</p>
            <div class="signature-line"></div>
            <p style="margin-top:4px;">NIP. _______________</p>
        </div>
        <div class="signature-box">
            <p>Kepala Sekolah</p>
            <div class="signature-line"></div>
            <p style="margin-top:4px;">NIP. _______________</p>
        </div>
    </div>

    <div class="footer">
        Dicetak pada {{ now()->format('d M Y H:i') }} — AcaHub Academic Management System
    </div>
</body>
</html>
