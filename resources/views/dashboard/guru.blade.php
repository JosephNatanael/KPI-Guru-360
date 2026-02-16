@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Dashboard Guru</h3>

    {{-- 1️⃣ Informasi Umum --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Informasi Guru</h5>
                    <p class="mb-1"><strong>Nama:</strong> {{ $guru->nama }}</p>

                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Periode</h5>
                         <form action="{{ route('dashboard.guru') }}" method="GET" class="d-flex align-items-center">
                            <select name="periode_id" class="form-select form-select-sm" onchange="this.form.submit()" style="max-width: 150px;">
                                @foreach($allPeriods as $p)
                                    <option value="{{ $p->id }}" {{ $periode && $periode->id == $p->id ? 'selected' : '' }}>
                                        {{ $p->tahun_ajaran }} - {{ ucfirst($p->semester) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    @if($periode)
                        <p class="mb-1">{{ $periode->tahun_ajaran }} - {{ ucfirst($periode->semester) }}</p>
                        <p class="mb-0">
                            <span class="badge {{ $statusPenilaian == 'Sudah Dinilai' ? 'bg-success' : 'bg-warning' }}">
                                {{ $statusPenilaian }}
                            </span>
                        </p>
                    @else
                        <p class="mb-0 text-muted">Non-Aktif</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Rekan Sudah Dinilai</h5>
                            <small class="card-text">Guru</small>
                        </div>
                        <h3 class="mb-0">{{ $rekanSudahDinilaiCount }}</h3>
                    </div>
                    <a href="javascript:void(0)" class="stretched-link" data-bs-toggle="modal" data-bs-target="#modalRekanSudah"></a>
                </div>
            </div>
            <div class="card text-bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Rekan Belum Dinilai</h5>
                            <small class="card-text">Guru</small>
                        </div>
                        <h3 class="mb-0">{{ $rekanBelumDinilaiCount }}</h3>
                    </div>
                    <a href="javascript:void(0)" class="stretched-link" data-bs-toggle="modal" data-bs-target="#modalRekanBelum"></a>
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

    {{-- 4️⃣ Persentase Kinerja Per Indikator --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Persentase Kinerja Per Indikator</h5>
                </div>
                <div class="card-body">
                    @if(count($indicatorPerformance) > 0)
                        <div style="position: relative; height: 400px;">
                            <canvas id="chartIndicatorPerformance"></canvas>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Belum ada data penilaian untuk ditampilkan pada grafik ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 5️⃣ Status Penilaian Rekan Sejawat (Moved to Top) --}}
    
    <!-- Modal Rekan Sudah Dinilai -->
    <div class="modal fade" id="modalRekanSudah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rekan Sudah Dinilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        @forelse($rekanSudahDinilaiList as $r)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $r['nama'] }}
                                <span class="badge bg-primary">{{ number_format($r['nilai'], 2) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">Belum ada rekan yang dinilai.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('evaluation.index') }}" class="btn btn-primary">Ke Halaman Penilaian</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rekan Belum Dinilai -->
    <div class="modal fade" id="modalRekanBelum" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rekan Belum Dinilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        @forelse($rekanBelumDinilaiList as $r)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $r['nama'] }}
                                <span class="badge bg-secondary">{{ $r['kelas'] }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">Semua rekan sudah dinilai.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('evaluation.index') }}" class="btn btn-primary">Mulai Penilaian</a>
                </div>
            </div>
        </div>
    </div>

    {{-- 6️⃣ Riwayat Penilaian --}}
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
                            <div class="mt-3" style="position: relative; height: 300px;">
                                <h6>Perbandingan dengan Periode Sebelumnya</h6>
                                <canvas id="chartRiwayat"></canvas>
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
    // Grafik Persentase Kinerja Per Indikator
    const ctxIndicator = document.getElementById('chartIndicatorPerformance');
    if (ctxIndicator) {
        const indicatorData = @json($indicatorPerformance);
        const labels = indicatorData.map(d => d.nama);
        const data = indicatorData.map(d => d.persentase);
        
        // Dynamic colors based on percentage
        const backgroundColors = data.map(val => {
            if (val >= 90) return 'rgba(75, 192, 192, 0.6)'; // Green
            if (val >= 75) return 'rgba(54, 162, 235, 0.6)'; // Blue
            if (val >= 60) return 'rgba(255, 206, 86, 0.6)'; // Yellow
            return 'rgba(255, 99, 132, 0.6)'; // Red
        });
        
        const borderColors = data.map(val => {
            if (val >= 90) return 'rgba(75, 192, 192, 1)';
            if (val >= 75) return 'rgba(54, 162, 235, 1)';
            if (val >= 60) return 'rgba(255, 206, 86, 1)';
            return 'rgba(255, 99, 132, 1)';
        });

        new Chart(ctxIndicator, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Persentase Kinerja (%)',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal Bar Chart for better readability of long labels
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Persentase (%)'
                        }
                    },
                    y: {
                        ticks: {
                            autoSkip: false, // Show all labels
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.x + '%';
                            }
                        }
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
                maintainAspectRatio: false,
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

