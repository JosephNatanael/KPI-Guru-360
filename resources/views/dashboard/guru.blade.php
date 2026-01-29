@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Dashboard Guru</h3>

    {{-- 1️⃣ Informasi Umum --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Informasi Guru</h5>
                    <p class="mb-1"><strong>Nama:</strong> {{ $guru->nama }}</p>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Periode & Status Penilaian</h5>
                    @if($periode)
                        <p class="mb-1"><strong>Tahun Ajaran:</strong> {{ $periode->tahun_ajaran }}</p>
                        <p class="mb-1"><strong>Semester:</strong> {{ ucfirst($periode->semester) }}</p>
                        <p class="mb-1"><strong>Tanggal:</strong> {{ $periode->tanggal_mulai }} s/d {{ $periode->tanggal_selesai }}</p>
                        <p class="mb-0">
                            <strong>Status:</strong> 
                            <span class="badge {{ $statusPenilaian == 'Sudah Dinilai' ? 'bg-success' : 'bg-warning' }}">
                                {{ $statusPenilaian }}
                            </span>
                        </p>
                    @else
                        <p class="mb-0 text-muted">Belum ada periode aktif</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 2️⃣ Nilai Akhir Kinerja --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Nilai Akhir Kinerja</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h6 class="text-muted">Nilai Persentase</h6>
                            @if($nilaiPersentase > 0)
                                <h2 class="mb-0 {{ $nilaiPersentase >= 70 ? 'text-success' : ($nilaiPersentase >= 55 ? 'text-warning' : 'text-danger') }}">
                                    {{ $nilaiPersentase }}%
                                </h2>
                            @else
                                <h2 class="mb-0 text-muted">-</h2>
                            @endif
                        </div>
                        <div class="col-md-4 text-center">
                            <h6 class="text-muted">Kategori Kinerja</h6>
                            <h4 class="mb-0">
                                @if($kategoriKinerja == 'Sangat Baik')
                                    <span class="badge bg-success">{{ $kategoriKinerja }}</span>
                                @elseif($kategoriKinerja == 'Baik')
                                    <span class="badge bg-info">{{ $kategoriKinerja }}</span>
                                @elseif($kategoriKinerja == 'Cukup')
                                    <span class="badge bg-warning">{{ $kategoriKinerja }}</span>
                                @elseif($kategoriKinerja == 'Perlu Perbaikan')
                                    <span class="badge bg-danger">{{ $kategoriKinerja }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $kategoriKinerja }}</span>
                                @endif
                            </h4>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6 class="text-muted">Rekomendasi Sistem</h6>
                            <h5 class="mb-0">{{ $rekomendasi }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    {{-- 3️⃣ Penilaian 360 Derajat --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Penilaian 360 Derajat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Nilai Kepala Sekolah</h6>
                                    @if($nilaiKepalaSekolah > 0)
                                        <h2 class="mb-0">{{ number_format($nilaiKepalaSekolah, 2) }}</h2>
                                        <small class="text-muted">Dari {{ $jumlahKepalaSekolah }} penilai</small>
                                    @else
                                        <h2 class="mb-0 text-muted">-</h2>
                                        <small class="text-muted">Belum dinilai</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Nilai Rata-rata Rekan Guru</h6>
                                    @if($nilaiRekanGuru > 0)
                                        <h2 class="mb-0">{{ number_format($nilaiRekanGuru, 2) }}</h2>
                                        <small class="text-muted">Dari {{ $jumlahRekanGuru }} penilai</small>
                                    @else
                                        <h2 class="mb-0 text-muted">-</h2>
                                        <small class="text-muted">Belum dinilai</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Nilai Rata-rata Wali Murid</h6>
                                    @if($guru->is_wali_kelas)
                                        @if($nilaiWaliMurid > 0)
                                            <h2 class="mb-0">{{ number_format($nilaiWaliMurid, 2) }}</h2>
                                            <small class="text-muted">Dari {{ $jumlahWaliMurid }} penilai</small>
                                        @else
                                            <h2 class="mb-0 text-muted">-</h2>
                                            <small class="text-muted">Belum dinilai</small>
                                        @endif
                                    @else
                                        <p class="mb-0 text-muted">Tidak berlaku (Bukan Wali Kelas)</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4️⃣ Ringkasan Kompetensi --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan Kompetensi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Pedagogik</h6>
                                    <h3 class="mb-0">{{ $kompetensiData['pedagogik'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Kepribadian</h6>
                                    <h3 class="mb-0">{{ $kompetensiData['kepribadian'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Sosial</h6>
                                    <h3 class="mb-0">{{ $kompetensiData['sosial'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Profesional</h6>
                                    <h3 class="mb-0">{{ $kompetensiData['profesional'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <canvas id="chartKompetensi" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5️⃣ Riwayat Penilaian --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Riwayat Penilaian</h5>
                </div>
                <div class="card-body">
                    @if(count($riwayatPenilaian) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Periode</th>
                                        <th>Nilai Akhir</th>
                                        <th>Rekomendasi</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayatPenilaian as $riwayat)
                                    <tr class="{{ $riwayat['is_current'] ? 'table-info' : '' }}">
                                        <td>{{ $riwayat['periode'] }}</td>
                                        <td>
                                            <strong>{{ $riwayat['nilai'] }}%</strong>
                                        </td>
                                        <td>{{ $riwayat['rekomendasi'] }}</td>
                                        <td>
                                            @if($riwayat['is_current'])
                                                <span class="badge bg-primary">Periode Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Periode Lalu</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if(count($riwayatPenilaian) > 1)
                            <div class="mt-3">
                                <h6>Perbandingan dengan Periode Sebelumnya</h6>
                                <canvas id="chartRiwayat" height="60"></canvas>
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">Belum ada riwayat penilaian</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Grafik Ringkasan Kompetensi
    const ctxKompetensi = document.getElementById('chartKompetensi');
    if (ctxKompetensi) {
        new Chart(ctxKompetensi, {
            type: 'bar',
            data: {
                labels: ['Pedagogik', 'Kepribadian', 'Sosial', 'Profesional'],
                datasets: [{
                    label: 'Nilai Kompetensi',
                    data: [
                        {{ $kompetensiData['pedagogik'] }},
                        {{ $kompetensiData['kepribadian'] }},
                        {{ $kompetensiData['sosial'] }},
                        {{ $kompetensiData['profesional'] }}
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(143, 60, 238, 0.6)',
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(156, 35, 226, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Grafik Riwayat Penilaian
    const ctxRiwayat = document.getElementById('chartRiwayat');
    if (ctxRiwayat) {
        const riwayatData = @json($riwayatPenilaian);
        const labels = riwayatData.map(r => r.periode);
        const nilai = riwayatData.map(r => r.nilai);
        
        new Chart(ctxRiwayat, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Akhir (%)',
                    data: nilai,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }
</script>
@endsection

