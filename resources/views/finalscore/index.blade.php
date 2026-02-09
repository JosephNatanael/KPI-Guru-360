@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fading show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fading show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- HEADER UTAMA --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">Hasil Nilai Akhir 360°</h4>
                <small class="text-muted">Periode {{ $periode->tahun_ajaran }}</small>
            </div>

            <div class="btn-group" role="group">
                <a href="{{ route('finalscore.hitung') }}" class="btn btn-primary btn-sm fw-semibold">
                    <i class="fas fa-calculator me-1"></i> Hitung Nilai
                </a>
                <button type="button" class="btn btn-info btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#catatanModal">
                    <i class="fas fa-info-circle me-1"></i> Catatan
                </button>
            </div>
        </div>
    </div>

    {{-- CARD REKAP NILAI AKHIR --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Rekap Nilai Akhir Guru</span>

                <a href="{{ route('reports.cetak-semua') }}"
                   target="_blank"
                   class="btn btn-sm btn-danger fw-semibold">
                    <i class="fas fa-print me-1"></i> Cetak Semua
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 finalscore-table">
                <thead class="table-light text-center">
                    <tr>
                        <th>Guru</th>
                        <th>KS</th>
                        <th>RG</th>
                        <th>WM</th>
                        <th>Nilai</th>
                        <th class="d-none d-md-table-cell">Rekomendasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($scores as $s)

                    @php
                        $nilai = $s->nilai_akhir;

                        if ($nilai >= 90) {
                            $recClass = 'success'; // hijau
                            $recIcon  = 'fa-award';
                        } elseif ($nilai >= 80) {
                            $recClass = 'primary'; // biru
                            $recIcon  = 'fa-thumbs-up';
                        } elseif ($nilai >= 51) {
                            $recClass = 'warning'; // kuning
                            $recIcon  = 'fa-circle-exclamation';
                        } else {
                            $recClass = 'danger'; // merah
                            $recIcon  = 'fa-triangle-exclamation';
                        }
                    @endphp

                    <tr>
                        <td class="fw-semibold">{{ $s->guru->nama }}</td>

                        <td class="text-center">{{ number_format($s->nilai_kepala_sekolah, 2) }}</td>
                        <td class="text-center">{{ number_format($s->nilai_rekan_guru, 2) }}</td>
                        <td class="text-center">{{ $s->nilai_wali_murid ?? '-' }}</td>

                        {{-- NILAI AKHIR --}}
                        <td class="text-center">
                            <span class="badge bg-success fs-6 px-3 py-2">
                                {{ number_format($s->nilai_akhir, 2) }}
                            </span>
                        </td>

                        {{-- REKOMENDASI --}}
                        <td class="text-center d-none d-md-table-cell">
                            <span class="badge bg-{{ $recClass }} fs-6 px-4 py-2 fw-semibold">
                                <i class="fas {{ $recIcon }} me-1"></i>
                                {{ strtoupper($s->recommendation->nama ?? '-') }}
                            </span>
                        </td>

                        {{-- AKSI --}}
                        <td class="text-center">
                            <a href="{{ route('reports.cetak-guru', $s->guru_id) }}"
                               target="_blank"
                               class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1"
                               title="Cetak Laporan PDF">
                                <i class="fas fa-file-pdf fs-5"></i>
                                <span class="fw-semibold">PDF</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        </div>
    </div>

    {{-- CARD RINGKASAN PERFORMA PER INDIKATOR --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="fas fa-chart-bar me-2"></i>Ringkasan Performa Per Indikator (Keseluruhan Guru)
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th class="text-start ps-4">Indikator</th>
                        <th style="width: 10%;">Bobot</th>
                        <th style="width: 15%;">Nilai Kontribusi</th>
                        <th style="width: 15%;">Persentase Kinerja</th>
                        <th style="width: 15%;">Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($indicatorPerformance as $ind)
                    <tr>
                        <td class="fw-semibold">{{ $ind['nama'] }}</td>
                        <td class="text-center">{{ $ind['bobot'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary fs-6 px-3 py-2">
                                {{ number_format($ind['nilai_kontribusi'], 2) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $ind['kategori_class'] }} fs-6 px-3 py-2">
                                {{ number_format($ind['persentase_kinerja'], 2) }}%
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $ind['kategori_class'] }} fs-6 px-4 py-2 fw-semibold">
                                {{ $ind['kategori_icon'] }} {{ strtoupper($ind['kategori']) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada data indikator.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <td colspan="2" class="text-end fw-bold">Total Nilai Kontribusi:</td>
                        <td class="text-center">
                            @php
                                $totalKontribusi = array_sum(array_column($indicatorPerformance, 'nilai_kontribusi'));
                            @endphp
                            <span class="badge bg-dark fs-6 px-3 py-2">
                                {{ number_format($totalKontribusi, 2) }}
                            </span>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="card-footer bg-light">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                <strong>Nilai Kontribusi</strong> = (Avg 360° / 5) × Bobot | 
                <strong>Persentase Kinerja</strong> = (Avg 360° / 5) × 100% | 
                <strong>Kategori</strong> ditentukan berdasarkan Persentase Kinerja
            </small>
        </div>
    </div>

    {{-- MODAL CATATAN PERHITUNGAN --}}
    <div class="modal fade" id="catatanModal" tabindex="-1" aria-labelledby="catatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="catatanModalLabel">
                        <i class="fas fa-calculator me-2"></i>Cara Perhitungan Nilai Akhir
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info border-0" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Sistem Penilaian 360°</strong> - Penilaian komprehensif dari berbagai perspektif
                    </div>

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-users me-2"></i>1. Nilai Per Role (KS, RG, WM)
                    </h6>
                    <p class="mb-3">
                        Setiap role (Kepala Sekolah, Rekan Guru, Wali Murid) memberikan penilaian terhadap guru.
                        Nilai per role dihitung dengan:
                    </p>
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <p class="mb-2"><strong>Nilai Role</strong> = Rata-rata dari semua nilai yang diberikan oleh role tersebut × Bobot Role</p>

                            <div class="alert alert-warning border-0 mb-3" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Bobot berbeda untuk Wali Kelas dan Non-Wali Kelas</strong>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kategori Guru</th>
                                            <th class="text-center">KS</th>
                                            <th class="text-center">RG</th>
                                            <th class="text-center">WM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Wali Kelas</strong></td>
                                            <td class="text-center">50%</td>
                                            <td class="text-center">30%</td>
                                            <td class="text-center">20%</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Non Wali Kelas</strong></td>
                                            <td class="text-center">70%</td>
                                            <td class="text-center">30%</td>
                                            <td class="text-center">0%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Bobot dapat disesuaikan di menu Master > Bobot Penilaian
                            </small>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-star me-2"></i>2. Nilai Akhir
                    </h6>
                    <p class="mb-3">
                        Nilai akhir diperoleh dari penjumlahan <strong>Nilai Kontribusi</strong> seluruh indikator penilaian.
                    </p>
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <p class="mb-2 fw-semibold">NILAI AKHIR = Σ (Nilai Kontribusi Indikator)</p>
                            <p class="mb-0 text-muted small">
                                Semakin tinggi kinerja pada indikator dengan bobot besar, semakin besar kontribusinya pada nilai akhir.
                            </p>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-calculator me-2"></i>3. Detail Perhitungan Indikator
                    </h6>
                    <p class="mb-3">
                        Setiap indikator memiliki dua metrik utama:
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card border-info h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-info mb-2 fw-bold">Nilai Kontribusi</h6>
                                    <div class="bg-light p-2 rounded mb-2 text-center font-monospace small">
                                        (Rata-rata 360° / 5) × Bobot Indikator
                                    </div>
                                    <small class="text-muted d-block">
                                        Menunjukkan besarnya sumbangsih indikator tersebut terhadap Nilai Akhir guru.
                                        Dipengaruhi oleh bobot indikator.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-success mb-2 fw-bold">Persentase Kinerja</h6>
                                    <div class="bg-light p-2 rounded mb-2 text-center font-monospace small">
                                        (Rata-rata 360° / 5) × 100%
                                    </div>
                                    <small class="text-muted d-block">
                                        Menunjukkan efektivitas kinerja pada indikator tersebut (skala 0-100%).
                                        Digunakan untuk menentukan kategori (Baik, Cukup, Kurang).
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-trophy me-2"></i>4. Rekomendasi
                    </h6>
                    <p class="mb-3">Berdasarkan nilai akhir, sistem memberikan rekomendasi:</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Rentang Nilai</th>
                                    <th>Rekomendasi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">90.00 - 100.00</td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-award me-1"></i>BERPRESTASI
                                        </span>
                                    </td>
                                    <td><small>Penetapan penghargaan, promosi jabatan</small></td>
                                </tr>
                                <tr>
                                    <td class="text-center">80.00 - 89.00</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-thumbs-up me-1"></i>DIPERTAHANKAN
                                        </span>
                                    </td>
                                    <td><small>Kesempatan pelatihan lanjutan untuk pengembangan diri</small></td>
                                </tr>
                                <tr>
                                    <td class="text-center">51.00 - 79.00</td>
                                    <td>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-circle-exclamation me-1"></i>PERLU PENINGKATAN
                                        </span>
                                    </td>
                                    <td><small>Pembinaan, pendampingan, pelatihan tambahan</small></td>
                                </tr>
                                <tr>
                                    <td class="text-center">0.00 - 50.00</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-triangle-exclamation me-1"></i>PERLU PERHATIAN KHUSUS
                                        </span>
                                    </td>
                                    <td><small>Evaluasi, pembinaan intensif dan/atau surat peringatan dari sekolah</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('styles')
<style>
/* Hover animation halus */
.finalscore-table tbody tr {
    transition: background-color .15s ease, transform .1s ease;
}

.finalscore-table tbody tr:hover {
    background-color: rgba(37, 99, 235, .05);

}

/* Badge lebih rapi */
.badge {
    letter-spacing: .3px;
}
</style>
@endsection
