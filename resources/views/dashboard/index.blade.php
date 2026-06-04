@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex align-items-center mb-4">
        <h3 class="mb-0">Dashboard {{ auth()->user()->role === 'admin' ? 'Admin' : 'Kepala Sekolah' }}</h3>
        @if(auth()->user()->role === 'kepala_sekolah' && auth()->user()->jenjang)
            <span class="badge bg-primary fs-6 ms-3">JENJANG {{ strtoupper(auth()->user()->jenjang) }}</span>
        @endif
    </div>

    @if(!$periode)
        <div class="alert alert-warning">
            Belum ada periode aktif.
        </div>
    @else
        {{-- 1️⃣ Informasi Umum & Filter --}}
        <div class="row mb-4">

    {{-- KIRI: Periode + Informasi Guru --}}
    <div class="col-md-9 mb-3">
        <div class="card h-100">
            <div class="card-body">

                {{-- ================= PERIODE ================= --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Periode Penilaian</h5>
                    <form action="{{ route('dashboard') }}" method="GET">
                        <select name="periode_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($allPeriods as $p)
                                <option value="{{ $p->id }}" {{ $periode->id == $p->id ? 'selected' : '' }}>
                                    {{ $p->tahun_ajaran }} - {{ ucfirst($p->semester) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <small class="text-muted d-block">Tahun Ajaran</small>
                <strong>{{ $periode->tahun_ajaran }}</strong>

                <div class="d-flex justify-content-between mt-1">
                    <span>Semester: <strong>{{ ucfirst($periode->semester) }}</strong></span>
                    <span class="badge bg-{{ $periode->status == 'aktif' ? 'success' : 'secondary' }}">
                        {{ ucfirst($periode->status) }}
                    </span>
                </div>

                <hr>

                {{-- ================= INFORMASI GURU ================= --}}
                <h6 class="fw-bold mb-2">Informasi Guru</h6>
                <p class="mb-2">
                    <strong>Total Guru:</strong> {{ $totalGuru }}
                </p>

                {{-- Progress Keseluruhan --}}
                <div class="d-flex justify-content-between mb-1">
                    <small>Progress Keseluruhan</small>
                    <small class="fw-bold">{{ $progressPercentage }}%</small>
                </div>
                <div class="progress mb-3" style="height: 12px;">
                    <div class="progress-bar {{ $progressPercentage == 100 ? 'bg-success' : 'bg-primary' }}"
                         style="width: {{ $progressPercentage }}%">
                    </div>
                </div>

                {{-- Detail Penilai --}}
                <div class="mb-2">
                    <small class="d-flex justify-content-between">
                        <span>Kepala Sekolah ({{ $countKepsekDone }}/{{ $countKepsekTotal }})</span>
                        <span>{{ $progressKepsek }}%</span>
                    </small>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-info" style="width: {{ $progressKepsek }}%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    <small class="d-flex justify-content-between">
                        <span>Rekan Guru ({{ $countGuruDone }}/{{ $countGuruTotal }})</span>
                        <span>{{ $progressGuru }}%</span>
                    </small>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-warning" style="width: {{ $progressGuru }}%"></div>
                    </div>
                </div>

                <div>
                    <small class="d-flex justify-content-between">
                        <span>Wali Murid ({{ $countWaliDone }}/{{ $countWaliTotal }})</span>
                        <span>{{ $progressWali }}%</span>
                    </small>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-danger" style="width: {{ $progressWali }}%"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
            {{-- Stats Stack Column --}}
            <div class="col-md-3 mb-3">
                @if(!$isAdmin)
                <div class="card text-bg-success mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Guru Sudah Dinilai</h5>
                            </div>
                            <h3 class="mb-0">{{ $guruSudahDinilai }}</h3>
                        </div>
                        <a href="javascript:void(0)" class="stretched-link" data-bs-toggle="modal" data-bs-target="#modalSudahDinilai"></a>
                    </div>
                </div>
                <div class="card text-bg-warning mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Guru Belum Dinilai</h5>
                            </div>
                            <h3 class="mb-0">{{ $guruBelumDinilai }}</h3>
                        </div>
                        <a href="javascript:void(0)" class="stretched-link" data-bs-toggle="modal" data-bs-target="#modalBelumDinilai"></a>
                    </div>
                </div>
                @endif

                {{-- Global Stats (Admin & Kepsek) --}}
                <div class="card text-bg-primary mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Nilai Rata-Rata</h6>
                        <h3 class="mb-0">{{ $rataRataNilai }}</h3>
                        <a href="javascript:void(0)" class="stretched-link" data-bs-toggle="modal" data-bs-target="#modalRataRata"></a>
                    </div>
                </div>
                @if(!$isAdmin)
                <div class="card text-bg-info">
                    <div class="card-body">
                        <h6 class="card-title">Guru Berprestasi</h6>
                        <h3 class="mb-0">{{ $jumlahGuruBerprestasi }}</h3>
                        <a href="javascript:void(0)" class="stretched-link" data-bs-toggle="modal" data-bs-target="#modalBerprestasi"></a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- 4️⃣ Grafik Tren --}}
        <div class="row mb-4">
             <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Tren Rata-rata Nilai Periode</span>
                        @if(count($trendLabels) > 10)
                        <div>
                            <button id="btnPrev" class="btn btn-sm bg-white border shadow-sm text-primary me-2" title="Periode Sebelumnya"><i class="fas fa-chevron-left"></i></button>
                            <button id="btnNext" class="btn btn-sm bg-white border shadow-sm text-primary" title="Periode Selanjutnya"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 300px;">
                            <canvas id="chartTrend"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2️⃣ INDIKATOR TERKUAT & TERLEMAH --}}
        @if($strongestIndicator && $weakestIndicator && !$isAdmin)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h6 class="text-white mb-0 fw-bold">
                            <i class="fas fa-bullseye me-2"></i>Analisis Indikator Kinerja
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Indikator Terkuat --}}
                            <div class="col-md-6">
                                <div class="card border-success mb-3 mb-md-0">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-trophy me-2"></i>Indikator Terkuat</h6>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold text-success">{{ $strongestIndicator['nama'] }}</h5>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div>
                                                <small class="text-muted d-block">Persentase Kinerja</small>
                                                <h3 class="mb-0 text-success">{{ number_format($strongestIndicator['persentase_kinerja'], 2) }}%</h3>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">Nilai Kontribusi</small>
                                                <h5 class="mb-0">{{ number_format($strongestIndicator['nilai_kontribusi'], 2) }}</h5>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <span class="badge bg-{{ $strongestIndicator['kategori_class'] }} fs-6 px-3 py-2">
                                                {{ $strongestIndicator['kategori_icon'] }} {{ strtoupper($strongestIndicator['kategori']) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Indikator Terlemah --}}
                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Indikator Terlemah</h6>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold text-warning">{{ $weakestIndicator['nama'] }}</h5>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div>
                                                <small class="text-muted d-block">Persentase Kinerja</small>
                                                <h3 class="mb-0 text-warning">{{ number_format($weakestIndicator['persentase_kinerja'], 2) }}%</h3>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">Nilai Kontribusi</small>
                                                <h5 class="mb-0">{{ number_format($weakestIndicator['nilai_kontribusi'], 2) }}</h5>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <span class="badge bg-{{ $weakestIndicator['kategori_class'] }} fs-6 px-3 py-2">
                                                {{ $weakestIndicator['kategori_icon'] }} {{ strtoupper($weakestIndicator['kategori']) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3 mb-0" role="alert">
                            <i class="fas fa-lightbulb me-2"></i>
                            <small>
                                <strong>Catatan:</strong> Kategori ditentukan berdasarkan <strong>Persentase Kinerja</strong>. 
                                <strong>Nilai Kontribusi</strong> menunjukkan kontribusi indikator terhadap nilai akhir.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- 3️⃣ Grafik Kinerja --}}
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        Grafik Persentase Kinerja Per Indikator
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 300px;">
                            <canvas id="chartKompetensi"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        Grafik Kategori Hasil Penilaian
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 300px;">
                            <canvas id="chartKategori"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

    @endif




    <!-- Modal Guru Sudah Dinilai -->
    <div class="modal fade" id="modalSudahDinilai" tabindex="-1" aria-labelledby="modalSudahDinilaiLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalSudahDinilaiLabel"><i class="fas fa-check-circle me-2"></i>Guru Sudah Dinilai</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Rata-rata Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($guruSudahDinilaiList as $g)
                                    <tr>
                                        <td>{{ $g['nama'] }}</td>
                                        <td><span class="badge bg-success">{{ number_format($g['nilai_akhir'], 2) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Belum ada data guru yang dinilai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                     @if($isAdmin)
                        <a href="{{ route('finalscore.index') }}" class="btn btn-outline-success">Lihat Selengkapnya</a>
                     @else
                        <a href="{{ route('evaluation.index') }}" class="btn btn-outline-success">Lihat Selengkapnya</a>
                     @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Guru Belum Dinilai -->
    <div class="modal fade" id="modalBelumDinilai" tabindex="-1" aria-labelledby="modalBelumDinilaiLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalBelumDinilaiLabel"><i class="fas fa-hourglass-half me-2"></i>Guru Belum Dinilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                        <tr>
                            <th>Nama Guru</th>
                            <th>Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guruBelumDinilaiList as $guru)
                        <tr>
                            <td>{{ $guru['nama'] }}</td>
                            <td>{{ $guru['kelas'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">Semua guru sudah dinilai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
                <div class="modal-footer">
                    @if($isAdmin)
                        <a href="{{ route('finalscore.unassessed') }}" class="btn btn-outline-warning text-dark">Lihat Selengkapnya</a>
                    @else
                        <a href="{{ route('evaluation.index') }}" class="btn btn-outline-warning text-dark">Mulai Penilaian</a>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rata-rata Nilai -->
    <div class="modal fade" id="modalRataRata" tabindex="-1" aria-labelledby="modalRataRataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalRataRataLabel"><i class="fas fa-users me-2"></i>Nilai Akhir Setiap Guru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Guru</th>
                                    <th class="text-center">Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scores as $score)
                                    <tr>
                                        <td>{{ $score->guru->nama }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success fs-6 px-3 py-2">
                                                {{ number_format($score->nilai_akhir, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Belum ada data nilai akhir.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td class="text-end fw-bold">Rata-rata:</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6 px-3 py-2">
                                            {{ number_format($rataRataNilai, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('finalscore.index') }}" class="btn btn-outline-primary">Detail Penilaian</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Guru Berprestasi -->
    <div class="modal fade" id="modalBerprestasi" tabindex="-1" aria-labelledby="modalBerprestasiLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalBerprestasiLabel"><i class="fas fa-trophy me-2"></i>Guru Berprestasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Nilai Akhir</th>
                                    <th>Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($guruBerprestasiList as $g)
                                    <tr>
                                        <td>{{ $g['nama'] }}</td>
                                        <td><span class="badge bg-info">{{ number_format($g['nilai_akhir'], 2) }}</span></td>
                                        <td>{{ $g['rekomendasi'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada guru berprestasi (Penghargaan/Promosi).</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('finalscore.index', ['filter' => 'berprestasi']) }}" class="btn btn-outline-info">Lihat Selengkapnya</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if($periode)
<script>
    // Grafik persentase kinerja per indikator
    const ctxKompetensi = document.getElementById('chartKompetensi').getContext('2d');
    const indicatorPerformance = {!! json_encode($indicatorPerformance) !!};
    const indicatorLabels = indicatorPerformance.map(item => item.nama);
    const indicatorValues = indicatorPerformance.map(item => item.persentase_kinerja);
    
    const indicatorBackgroundColors = indicatorValues.map(val => {
        if (val >= 90) return 'rgba(40, 167, 69, 0.6)'; // Green
        if (val >= 80) return 'rgba(0, 123, 255, 0.6)'; // Blue
        if (val >= 51) return 'rgba(255, 193, 7, 0.6)'; // Yellow
        return 'rgba(220, 53, 69, 0.6)'; // Red
    });
    
    const indicatorBorderColors = indicatorValues.map(val => {
        if (val >= 90) return 'rgba(40, 167, 69, 1)';
        if (val >= 80) return 'rgba(0, 123, 255, 1)';
        if (val >= 51) return 'rgba(255, 193, 7, 1)';
        return 'rgba(220, 53, 69, 1)';
    });

    new Chart(ctxKompetensi, {
        type: 'bar',
        data: {
            labels: indicatorLabels,
            datasets: [{
                label: 'Persentase Kinerja (%)',
                data: indicatorValues,
                backgroundColor: indicatorBackgroundColors,
                borderColor: indicatorBorderColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const val = context.parsed.y;
                            const recText = indicatorPerformance[context.dataIndex].kategori;
                            return 'Kinerja: ' + val.toFixed(2) + '% (' + recText + ')';
                        }
                    }
                }
            }
        }
    });

    // Grafik kategori hasil penilaian - warna dinamis berdasarkan jumlah kategori
    const ctxKategori = document.getElementById('chartKategori').getContext('2d');
    const kategoriLabels = {!! json_encode($kategoriLabels) !!};
    const kategoriCounts = {!! json_encode($kategoriCounts) !!};
    
    // Fungsi untuk generate warna secara dinamis
    function generateColors(count) {
        if (count === 0) {
            return { backgrounds: [], borders: [] };
        }
        
        const colorPalette = [
            { bg: 'rgba(87, 241, 56, 1)', border: 'rgba(87, 241, 56, 1)' },   // Teal
            { bg: 'rgba(44, 164, 211, 1)', border: 'rgba(44, 164, 211, 1)' },   // Yellow
            { bg: 'rgba(236, 184, 13, 0.6)', border: 'rgba(236, 184, 13, 0.6)' },   // Red
            { bg: 'rgba(241, 6, 6, 0.6)', border: 'rgba(241, 12, 12, 0.96)' },  // Purple
            { bg: 'rgba(54, 162, 235, 0.6)', border: 'rgba(54, 162, 235, 1)' },   // Blue
            { bg: 'rgba(255, 159, 64, 0.6)', border: 'rgba(255, 159, 64, 1)' },    // Orange
            { bg: 'rgba(201, 203, 207, 0.6)', border: 'rgba(201, 203, 207, 1)' },  // Grey
            { bg: 'rgba(255, 205, 86, 0.6)', border: 'rgba(255, 205, 86, 1)' },     // Gold
            { bg: 'rgba(75, 192, 192, 0.6)', border: 'rgba(75, 192, 192, 1)' },   // Cyan
            { bg: 'rgba(255, 99, 132, 0.6)', border: 'rgba(211, 62, 94, 1)' },    // Pink
        ];
        
        const backgrounds = [];
        const borders = [];
        
        for (let i = 0; i < count; i++) {
            const color = colorPalette[i % colorPalette.length];
            backgrounds.push(color.bg);
            borders.push(color.border);
        }
        
        return { backgrounds, borders };
    }
    
    // Hanya buat grafik jika ada kategori
    if (kategoriLabels.length > 0) {
        const colors = generateColors(kategoriLabels.length);
        
        new Chart(ctxKategori, {
            type: 'doughnut',
            data: {
                labels: kategoriLabels,
                datasets: [{
                    data: kategoriCounts,
                    backgroundColor: colors.backgrounds,
                    borderColor: colors.borders,
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                maintainAspectRatio: false,
                onClick: (e, activeEls) => {
                    if (activeEls.length > 0) {
                        const index = activeEls[0].index;
                        const label = kategoriLabels[index];
                        window.location.href = "{{ route('finalscore.index') }}?rekomendasi=" + encodeURIComponent(label);
                    }
                }
            }
        });
    } else {
        // Tampilkan pesan jika tidak ada kategori
        ctxKategori.canvas.parentElement.innerHTML = '<div class="alert alert-info text-center">Belum ada data rekomendasi. Silakan tambahkan rekomendasi di menu Rekomendasi.</div>';
    }
    
    // Grafik Tren Rata-rata
    const ctxTrend = document.getElementById('chartTrend').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: {!! json_encode($trendLabels) !!},
            datasets: [{
                label: 'Rata-rata Nilai Akhir',
                data: {!! json_encode($trendData) !!},
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
</script>
@endif
@endsection
