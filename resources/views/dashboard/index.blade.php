@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Dashboard Kepala Sekolah</h3>

    @if(!$periode)
        <div class="alert alert-warning">
            Belum ada periode aktif.
        </div>
    @else
        {{-- 1️⃣ Informasi Umum --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Periode Penilaian Aktif</h5>
                        <p class="mb-1"><strong>Tahun Ajaran:</strong> {{ $periode->tahun_ajaran }}</p>
                        <p class="mb-1"><strong>Semester:</strong> {{ ucfirst($periode->semester) }}</p>
                        <p class="mb-0"><strong>Tanggal:</strong> {{ $periode->tanggal_mulai }} s/d {{ $periode->tanggal_selesai }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Guru</h5>
                        <p class="mb-0">
                            <strong>Total Guru:</strong> {{ $totalGuru }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2️⃣ Ringkasan Statistik --}}
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-bg-success h-100">
                    <div class="card-body">
                        <h6 class="card-title">Guru Sudah Dinilai</h6>
                        <h3 class="mb-0">{{ $guruSudahDinilai }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-bg-warning h-100">
                    <div class="card-body">
                        <h6 class="card-title">Guru Belum Dinilai</h6>
                        <h3 class="mb-0">{{ $guruBelumDinilai }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-bg-primary h-100">
                    <div class="card-body">
                        <h6 class="card-title">Rata-rata Nilai Kinerja</h6>
                        <h3 class="mb-0">{{ $rataRataNilai }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-bg-info h-100">
                    <div class="card-body">
                        <h6 class="card-title">Guru Berprestasi</h6>
                        <h3 class="mb-0">{{ $jumlahGuruBerprestasi }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3️⃣ Grafik Kinerja --}}
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        Grafik Rata-rata Nilai per Kompetensi
                    </div>
                    <div class="card-body">
                        <canvas id="chartKompetensi" height="150"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        Grafik Kategori Hasil Penilaian
                    </div>
                    <div class="card-body">
                        <canvas id="chartKategori" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if($periode)
<script>
    // Grafik rata-rata nilai per kompetensi
    const ctxKompetensi = document.getElementById('chartKompetensi').getContext('2d');
    new Chart(ctxKompetensi, {
        type: 'bar',
        data: {
            labels: {!! json_encode($kompetensiLabels) !!},
            datasets: [{
                label: 'Rata-rata Nilai',
                data: {!! json_encode($kompetensiScores) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5
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
            { bg: 'rgba(75, 192, 192, 0.6)', border: 'rgba(75, 192, 192, 1)' },   // Teal
            { bg: 'rgba(255, 206, 86, 0.6)', border: 'rgba(255, 206, 86, 1)' },   // Yellow
            { bg: 'rgba(255, 99, 132, 0.6)', border: 'rgba(255, 99, 132, 1)' },   // Red
            { bg: 'rgba(153, 102, 255, 0.6)', border: 'rgba(153, 102, 255, 1)' },  // Purple
            { bg: 'rgba(54, 162, 235, 0.6)', border: 'rgba(54, 162, 235, 1)' },   // Blue
            { bg: 'rgba(255, 159, 64, 0.6)', border: 'rgba(255, 159, 64, 1)' },    // Orange
            { bg: 'rgba(201, 203, 207, 0.6)', border: 'rgba(201, 203, 207, 1)' },  // Grey
            { bg: 'rgba(255, 205, 86, 0.6)', border: 'rgba(255, 205, 86, 1)' },     // Gold
            { bg: 'rgba(75, 192, 192, 0.6)', border: 'rgba(75, 192, 192, 1)' },   // Cyan
            { bg: 'rgba(255, 99, 132, 0.6)', border: 'rgba(255, 99, 132, 1)' },    // Pink
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
                }
            }
        });
    } else {
        // Tampilkan pesan jika tidak ada kategori
        ctxKategori.canvas.parentElement.innerHTML = '<div class="alert alert-info text-center">Belum ada data rekomendasi. Silakan tambahkan rekomendasi di menu Rekomendasi.</div>';
    }
</script>
@endif
@endsection
