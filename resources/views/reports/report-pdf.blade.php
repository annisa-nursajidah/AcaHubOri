<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor — {{ $student->user->name }}</title>
    <style>
        /* ─── Reset & Base ─────────────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.6;
            background: #fff;
        }

        /* ─── Page Container ───────────────────────────────────── */
        .page {
            padding: 30px 40px;
        }

        /* ─── Header ───────────────────────────────────────────── */
        .header {
            text-align: center;
            padding-bottom: 14px;
            margin-bottom: 20px;
            border-bottom: 3px double #0e7490;
            position: relative;
        }
        .header::after {
            content: '';
            display: block;
            height: 1px;
            background: #0e7490;
            margin-top: 3px;
        }
        .header .school-name {
            font-size: 18px;
            font-weight: 700;
            color: #0e7490;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 2px;
        }
        .header .school-address {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 6px;
        }
        .header .doc-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 2px;
            background: linear-gradient(90deg, transparent, #f0fdfa, transparent);
            padding: 4px 0;
            margin-top: 6px;
        }
        .header .doc-subtitle {
            font-size: 10px;
            color: #475569;
            margin-top: 2px;
        }

        /* ─── Student Info ─────────────────────────────────────── */
        .student-info {
            width: 100%;
            margin-bottom: 18px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }
        .student-info-header {
            background: #0e7490;
            color: #fff;
            padding: 6px 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .student-info-body {
            padding: 10px 12px;
            background: #f8fafc;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .info-label {
            font-weight: 600;
            color: #334155;
            width: 120px;
            white-space: nowrap;
        }
        .info-sep {
            width: 10px;
            color: #334155;
        }
        .info-value {
            color: #475569;
        }

        /* ─── Grade Table ──────────────────────────────────────── */
        .section-title {
            background: #0e7490;
            color: #fff;
            padding: 6px 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 6px 6px 0 0;
            margin-top: 4px;
        }
        table.grades {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-top: none;
        }
        table.grades th {
            background: #f1f5f9;
            color: #334155;
            padding: 7px 8px;
            text-align: center;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #e2e8f0;
        }
        table.grades th:nth-child(1) { width: 30px; }
        table.grades th:nth-child(2) { text-align: left; width: auto; }
        table.grades th:nth-child(3) { text-align: left; width: 90px; }
        table.grades td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
        }
        table.grades td:nth-child(1) { text-align: center; color: #64748b; }
        table.grades td:nth-child(2) { text-align: left; font-weight: 600; color: #1e293b; }
        table.grades td:nth-child(3) { text-align: left; color: #475569; font-size: 9px; }
        table.grades tr:nth-child(even) td {
            background: #f8fafc;
        }
        table.grades tfoot td {
            background: #f0fdfa !important;
            font-weight: 700;
            border-top: 2px solid #0e7490;
        }

        /* ─── Grade Colors ─────────────────────────────────────── */
        .grade-high { color: #15803d; font-weight: 700; }
        .grade-mid  { color: #d97706; font-weight: 700; }
        .grade-low  { color: #dc2626; font-weight: 700; }

        .status-tuntas {
            color: #15803d;
            font-weight: 700;
            font-size: 9px;
        }
        .status-belum {
            color: #dc2626;
            font-weight: 700;
            font-size: 9px;
        }

        /* ─── Summary Box ──────────────────────────────────────── */
        .summary {
            margin-top: 16px;
            border: 1px solid #99f6e4;
            border-radius: 6px;
            overflow: hidden;
        }
        .summary-header {
            background: #0e7490;
            color: #fff;
            padding: 6px 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .summary-body {
            padding: 10px 12px;
            background: #f0fdfa;
        }
        .summary-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-grid td {
            padding: 3px 6px;
        }
        .summary-label {
            font-weight: 600;
            color: #0e7490;
            width: 160px;
        }
        .summary-value {
            color: #1e293b;
            font-weight: 700;
        }

        /* ─── Weight Note ──────────────────────────────────────── */
        .weight-note {
            margin-top: 12px;
            padding: 8px 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            font-size: 8px;
            color: #1e40af;
        }

        /* ─── Signature ────────────────────────────────────────── */
        .signature-area {
            margin-top: 40px;
            width: 100%;
        }
        .signature-area td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }
        .signature-title {
            font-size: 10px;
            color: #334155;
            font-weight: 600;
        }
        .signature-date {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 4px;
        }
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #334155;
            width: 160px;
            display: inline-block;
        }
        .signature-nip {
            font-size: 8px;
            color: #64748b;
            margin-top: 3px;
        }

        /* ─── Footer ───────────────────────────────────────────── */
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #94a3b8;
            font-size: 8px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ═══ Header ═══════════════════════════════════════════════ --}}
    <div class="header">
        <div class="school-name">{{ $student->user->school->name ?? 'AcaHub School' }}</div>
        <div class="school-address">{{ $student->user->school->address ?? '' }}</div>
        <div class="doc-title">LAPORAN HASIL BELAJAR PESERTA DIDIK</div>
        <div class="doc-subtitle">Tahun Pelajaran {{ $tahunAjaran }} — Semester {{ $semester }}</div>
    </div>

    {{-- ═══ Student Info ═════════════════════════════════════════ --}}
    <div class="student-info">
        <div class="student-info-header">Data Peserta Didik</div>
        <div class="student-info-body">
            <table class="info-table">
                <tr>
                    <td class="info-label">Nama Lengkap</td>
                    <td class="info-sep">:</td>
                    <td class="info-value"><strong>{{ $student->user->name }}</strong></td>
                    <td style="width: 20px;"></td>
                    <td class="info-label">Semester</td>
                    <td class="info-sep">:</td>
                    <td class="info-value">{{ $semester }}</td>
                </tr>
                <tr>
                    <td class="info-label">NIS</td>
                    <td class="info-sep">:</td>
                    <td class="info-value">{{ $student->nis ?? '—' }}</td>
                    <td></td>
                    <td class="info-label">Tahun Ajaran</td>
                    <td class="info-sep">:</td>
                    <td class="info-value">{{ $tahunAjaran }}</td>
                </tr>
                <tr>
                    <td class="info-label">Kelas</td>
                    <td class="info-sep">:</td>
                    <td class="info-value">{{ $student->kelas ?? '—' }}</td>
                    <td></td>
                    <td class="info-label">Tanggal Cetak</td>
                    <td class="info-sep">:</td>
                    <td class="info-value">{{ now()->translatedFormat('d F Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ═══ Grades Table ═════════════════════════════════════════ --}}
    <div class="section-title">Capaian Hasil Belajar</div>
    <table class="grades">
        <thead>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Guru Pengampu</th>
                <th>Tugas</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Praktik</th>
                <th>Rata-rata</th>
                <th>Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjectGrades as $i => $sg)
            @php
                $avgClass = $sg->average >= 75 ? 'grade-high' : ($sg->average >= 50 ? 'grade-mid' : 'grade-low');
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $sg->subject->nama }}</td>
                <td>{{ $sg->teacher }}</td>
                <td>{{ $sg->tugas !== null ? number_format($sg->tugas, 1) : '—' }}</td>
                <td>{{ $sg->uts !== null ? number_format($sg->uts, 1) : '—' }}</td>
                <td>{{ $sg->uas !== null ? number_format($sg->uas, 1) : '—' }}</td>
                <td>{{ $sg->praktik !== null ? number_format($sg->praktik, 1) : '—' }}</td>
                <td class="{{ $avgClass }}">{{ number_format($sg->average, 1) }}</td>
                <td class="{{ $sg->status === 'Tuntas' ? 'status-tuntas' : 'status-belum' }}">{{ $sg->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center; padding:24px; color:#94a3b8; font-style:italic;">
                    Belum ada data nilai untuk semester ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($subjectGrades->count() > 0)
        <tfoot>
            <tr>
                <td colspan="7" style="text-align:right; padding-right:12px;">
                    Rata-rata Keseluruhan
                </td>
                @php
                    $overallClass = $overallAvg >= 75 ? 'grade-high' : ($overallAvg >= 50 ? 'grade-mid' : 'grade-low');
                    $overallStatus = $overallAvg >= 75 ? 'Tuntas' : 'Belum Tuntas';
                @endphp
                <td class="{{ $overallClass }}" style="font-size:12px;">
                    {{ number_format($overallAvg, 1) }}
                </td>
                <td class="{{ $overallAvg >= 75 ? 'status-tuntas' : 'status-belum' }}">
                    {{ $overallStatus }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- ═══ Summary ══════════════════════════════════════════════ --}}
    @if($subjectGrades->count() > 0)
    <div class="summary">
        <div class="summary-header">Ringkasan Penilaian</div>
        <div class="summary-body">
            <table class="summary-grid">
                <tr>
                    <td class="summary-label">Jumlah Mata Pelajaran</td>
                    <td class="info-sep">:</td>
                    <td class="summary-value">{{ $subjectGrades->count() }} mapel</td>
                    <td style="width:20px;"></td>
                    <td class="summary-label">Rata-rata Keseluruhan</td>
                    <td class="info-sep">:</td>
                    <td class="summary-value {{ $overallClass }}">{{ number_format($overallAvg, 1) }}</td>
                </tr>
                <tr>
                    <td class="summary-label">Tuntas (≥ 75)</td>
                    <td class="info-sep">:</td>
                    <td class="summary-value" style="color:#15803d;">{{ $subjectGrades->where('status', 'Tuntas')->count() }} mapel</td>
                    <td></td>
                    <td class="summary-label">Belum Tuntas (< 75)</td>
                    <td class="info-sep">:</td>
                    <td class="summary-value" style="color:#dc2626;">{{ $subjectGrades->where('status', 'Belum Tuntas')->count() }} mapel</td>
                </tr>
                <tr>
                    <td class="summary-label">Nilai Tertinggi</td>
                    <td class="info-sep">:</td>
                    <td class="summary-value">{{ number_format($subjectGrades->max('average'), 1) }}</td>
                    <td></td>
                    <td class="summary-label">Nilai Terendah</td>
                    <td class="info-sep">:</td>
                    <td class="summary-value">{{ number_format($subjectGrades->min('average'), 1) }}</td>
                </tr>
            </table>
        </div>
    </div>
    @endif

    {{-- ═══ Weight Note ══════════════════════════════════════════ --}}
    <div class="weight-note">
        <strong>Keterangan Bobot Penilaian:</strong>
        Tugas 25% · UTS 25% · UAS 35% · Praktik 15% · Kriteria Ketuntasan Minimal (KKM): 75
    </div>

    {{-- ═══ Signature ════════════════════════════════════════════ --}}
    <table class="signature-area">
        <tr>
            <td>
                <div class="signature-title">Wali Kelas</div>
                <div class="signature-line"></div>
                <div class="signature-nip">NIP. ___________________</div>
            </td>
            <td>
                <div class="signature-date">
                    ................, {{ now()->translatedFormat('d F Y') }}
                </div>
                <div class="signature-title">Kepala Sekolah</div>
                <div class="signature-line"></div>
                <div class="signature-nip">NIP. ___________________</div>
            </td>
        </tr>
    </table>

    {{-- ═══ Footer ═══════════════════════════════════════════════ --}}
    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh sistem AcaHub pada {{ now()->format('d/m/Y H:i') }} WIB
        &nbsp;•&nbsp; Dokumen ini sah tanpa tanda tangan basah
    </div>

</div>
</body>
</html>
