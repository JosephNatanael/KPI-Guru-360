<!DOCTYPE html>
<html>
<head>
    <title>Laporan KPI {{ $guru->nama }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h1, h2, h3 { margin: 5px 0; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; text-transform: uppercase; }
        
        .section-title { font-size: 13px; font-weight: bold; margin-bottom: 5px; margin-top: 15px; text-decoration: underline; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 4px; vertical-align: top; }
        
        .table-bordered { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .table-bordered th, .table-bordered td { border: 1px solid #000; padding: 5px; }
        .table-bordered th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .footer-note { margin-top: 30px; font-style: italic; font-size: 10px; border-top: 1px solid #ccc; padding-top: 5px; }
        .score-box { border: 2px solid #000; padding: 10px; text-align: center; width: 150px; margin-right: 20px; float: left; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ config('KPI GURU 360°') }} HASIL PENILAIAN KINERJA</h1>
    </div>

    <!-- 1. IDENTITAS GURU -->
    <div class="section-title">1. IDENTITAS GURU</div>
    <table class="info-table">
        <tr>
            <td style="width: 25%">Nama Guru</td>
            <td>: <strong>{{ $guru->nama }}</strong></td>
        </tr>
        <tr>
            <td>Jenjang</td>
            <td>: {{ strtoupper($guru->jenjang ?? '-') }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>: {{ $guru->is_wali_kelas ? 'Wali Kelas' : 'Guru Mapel' }}</td>
        </tr>
        <tr>
            <td>Periode Penilaian</td>
            <td>: {{ $periode->tahun_ajaran }} ({{ date('d-m-Y', strtotime($periode->tanggal_mulai)) }} s.d. {{ date('d-m-Y', strtotime($periode->tanggal_selesai)) }})</td>
        </tr>
    </table>

    <!-- 2. HASIL AKHIR PENILAIAN -->
    <div class="section-title">2. HASIL AKHIR PENILAIAN</div>
    <table style="width: 100%; border: none; margin-bottom: 10px;">
        <tr>
            <td style="width: 180px; vertical-align: top;">
                <div class="score-box">
                    NILAI AKHIR<br>
                    <strong style="font-size: 24px;">{{ number_format($finalScore->nilai_akhir, 2) }}</strong>
                </div>
            </td>
            <td style="vertical-align: top; padding-left: 20px;">
                @php
                     $kategori = 'Cukup';
                     if($finalScore->nilai_akhir >= 90) $kategori = 'Sangat Baik';
                     elseif($finalScore->nilai_akhir >= 80) $kategori = 'Baik';
                     elseif($finalScore->nilai_akhir >= 51) $kategori = 'Cukup';
                     else $kategori = 'Kurang';
                @endphp
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="width: 140px; font-weight: bold; padding: 2px;">Kategori Kinerja</td>
                        <td style="padding: 2px;">: {{ $kategori }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; vertical-align: top; padding: 2px;">Rekomendasi Sistem</td>
                        <td style="vertical-align: top; padding: 2px;">: {{ $finalScore->rekomendasi ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 3. REKAP PENILAIAN 360 DERAJAT -->
    <br>
    <div class="section-title">3. REKAP PENILAIAN 360 DERAJAT</div>
    <table class="table-bordered">
        <thead>
            <tr>
                <th>Evaluator</th>
                <th>Bobot</th>
                <th>Nilai Terbobot</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Kepala Sekolah</td>
                <td class="text-center">{{ $bobotEvaluator->kepala_sekolah ?? 0 }}%</td>
                <td class="text-center">{{ number_format($finalScore->nilai_kepala_sekolah, 2) }}</td>
            </tr>
            <tr>
                <td>Rekan Guru (Rata-rata)</td>
                <td class="text-center">{{ $bobotEvaluator->rekan_guru ?? 0 }}%</td>
                <td class="text-center">{{ number_format($finalScore->nilai_rekan_guru, 2) }}</td>
            </tr>
            @if($guru->is_wali_kelas)
            <tr>
                <td>Wali Murid (Rata-rata)</td>
                <td class="text-center">{{ $bobotEvaluator->wali_murid ?? 0 }}%</td>
                <td class="text-center">{{ number_format($finalScore->nilai_wali_murid, 2) }}</td>
            </tr>
            @else
            <tr>
                <td>Wali Murid</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- 4. RINGKASAN NILAI PER KOMPETENSI -->
    <div class="section-title">4. RINGKASAN NILAI PER KOMPETENSI</div>
    <table class="table-bordered">
        <thead>
            <tr>
                <th>Kompetensi</th>
                <th>Nilai Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($competencies as $compName => $score)
            <tr>
                <td>{{ $compName }}</td>
                <td class="text-center">{{ number_format($score, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 5. DETAIL INDIKATOR KPI -->
    <div class="section-title">5. DETAIL INDIKATOR KPI</div>
    <table class="table-bordered">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th>Indikator</th>
                <th style="width: 20%">Kompetensi</th>
                <th style="width: 15%">Nilai (360)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($indicatorDetails as $idx => $detail)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $detail['nama'] }}</td>
                <td class="text-center">{{ $detail['kompetensi'] }}</td>
                <td class="text-center">{{ number_format($detail['nilai_akhir'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 6. PENUTUP -->
    <div class="footer-note">
        <strong>Catatan:</strong> Laporan ini bersifat evaluatif untuk pengembangan kinerja guru secara berkelanjutan.
    </div>

    <div style="margin-top: 40px;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%; text-align: center;">
                    <p>Manado, {{ date('d F Y') }}</p>
                    <p>Mengetahui Kepala Sekolah</p>
                    <br><br><br>
                    <p>__________________________</p>
                    <p>(Stempel & Tanda Tangan)</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
