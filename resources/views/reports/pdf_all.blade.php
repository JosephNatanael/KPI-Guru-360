<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekap KPI Guru</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header h2 { margin: 5px 0; font-size: 14px; }
        .header p { margin: 2px 0; }
        
        .summary-section { margin-bottom: 20px; width: 100%; }
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .summary-table td { padding: 4px; vertical-align: top; }
        
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 6px; text-align: left; }
        .main-table th { background-color: #f2f2f2; text-align: center; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .footer-note { margin-top: 20px; font-style: italic; font-size: 10px; border-top: 1px solid #ccc; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>KPI GURU 360°</h1>
        <h2>Laporan Rekap Penilaian Kinerja Guru</h2>
        <p>Periode Penilaian: {{ $periode->tahun_ajaran }} ({{ date('d-m-Y', strtotime($periode->tanggal_mulai)) }} s.d. {{ date('d-m-Y', strtotime($periode->tanggal_selesai)) }})</p>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <!-- Ringkasan Umum -->
    <div class="summary-section">
        <h3>Ringkasan Umum</h3>
        <table class="summary-table">
            <tr>
                <td style="width: 30%"><strong>Total Guru</strong></td>
                <td>: {{ $totalGuru }} Guru</td>
            </tr>
            <tr>
                <td><strong>Rata-rata Nilai Kinerja Sekolah</strong></td>
                <td>: {{ number_format($rataRataSekolah, 2) }}</td>
            </tr>
        </table>
        
        <table class="summary-table" style="margin-top: 10px;">
            <tr>
                <td colspan="2"><strong>Jumlah Guru per Kategori:</strong></td>
            </tr>
            <tr>
                <td style="width: 30%">- Penghargaan</td>
                <td>: {{ $stats['Penghargaan'] }}</td>
            </tr>
            <tr>
                <td>- Pelatihan</td>
                <td>: {{ $stats['Pelatihan'] }}</td>
            </tr>
            <tr>
                <td>- Pembinaan</td>
                <td>: {{ $stats['Pembinaan'] }}</td>
            </tr>
            <tr>
                <td>- Evaluasi</td>
                <td>: {{ $stats['Evaluasi'] }}</td>
            </tr>
        </table>
    </div>

    <!-- Tabel Rekap Nilai Guru -->
    <h3>Tabel Rekap Nilai Guru</h3>
    <table class="main-table" style="margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 25%">Nama Guru</th>
                <th style="width: 15%">Status</th>
                <th style="width: 10%">Nilai Akhir (%)</th>
                <th style="width: 20%">Kategori Kinerja</th>
                <th style="width: 25%">Rekomendasi Sistem</th>
            </tr>
        </thead>
        <tbody>
            @forelse($scores as $index => $score)
                @php
                    // Logika sederhana untuk kategori kinerja berdasarkan nilai (bisa disesuaikan)
                    // Misal: > 90 Sangat Baik, > 80 Baik, etc.
                    // Atau ambil dari rekomendasi jika mengandung kata tertentu.
                    // Disini saya buat simulasi logic sederhana atau ambil dari rekomendasi.
                    $kategori = 'Cukup';
                    if($score->nilai_akhir >= 90) $kategori = 'Sangat Baik';
                    elseif($score->nilai_akhir >= 80) $kategori = 'Baik';
                    elseif($score->nilai_akhir >= 70) $kategori = 'Cukup';
                    else $kategori = 'Kurang';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $score->guru->nama }}</td>
                    <td class="text-center">{{ $score->guru->is_wali_kelas ? 'Wali Kelas' : 'Guru Mapel' }}</td>
                    <td class="text-center"><strong>{{ number_format($score->nilai_akhir, 2) }}</strong></td>
                    <td class="text-center">{{ $kategori }}</td>
                    <td>{{ $score->rekomendasi ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data nilai akhir untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Ringkasan Performa Per Indikator -->
    <div style="page-break-inside: avoid;">
        <h3>Ringkasan Performa Per Indikator (Keseluruhan Guru)</h3>
        <table class="main-table" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 35%">Indikator</th>
                    <th style="width: 10%">Bobot</th>
                    <th style="width: 15%">Nilai Kontribusi</th>
                    <th style="width: 15%">Persentase Kinerja</th>
                    <th style="width: 20%">Kategori</th>
                </tr>
            </thead>
            <tbody>
                @php $totalKontribusi = 0; @endphp
                @foreach($indicatorPerformance as $index => $ind)
                    @php $totalKontribusi += $ind['nilai_kontribusi']; @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $ind['nama'] }}</td>
                        <td class="text-center">{{ $ind['bobot'] }}</td>
                        <td class="text-center">{{ number_format($ind['nilai_kontribusi'], 2) }}</td>
                        <td class="text-center">{{ number_format($ind['persentase_kinerja'], 2) }}%</td>
                        <td class="text-center">{{ $ind['kategori'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total Nilai Kontribusi:</strong></td>
                    <td class="text-center" style="background-color: #f2f2f2;"><strong>{{ number_format($totalKontribusi, 2) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Catatan Sistem -->
    <div class="footer-note">
        <strong>Catatan Sistem:</strong><br>
        - Penilaian menggunakan KPI & metode 360 derajat.<br>
        - Digunakan sebagai dasar pembinaan dan pengembangan guru.
    </div>

    <div style="margin-top: 40px; width: 30%; float: right; text-align: center;">
        <p>Mengetahui,<br>Kepala Sekolah</p>
        <br><br><br>
        <p>(................................)</p>
    </div>

</body>
</html>
