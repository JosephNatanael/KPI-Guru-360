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
                        <th class="d-none d-sm-table-cell">KS</th>
                        <th class="d-none d-sm-table-cell">RG</th>
                        <th class="d-none d-sm-table-cell">WM</th>
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

                        <td class="text-center d-none d-sm-table-cell">{{ number_format($s->nilai_kepala_sekolah, 2) }}</td>
                        <td class="text-center d-none d-sm-table-cell">{{ number_format($s->nilai_rekan_guru, 2) }}</td>
                        <td class="text-center d-none d-sm-table-cell">{{ $s->nilai_wali_murid ?? '-' }}</td>

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

    {{-- CARD RATA-RATA NILAI PER KOMPETENSI --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-secondary text-white fw-semibold">
            Rata-rata Nilai Per Kompetensi
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th class="text-start ps-4">Guru</th>
                        <th>Pedagogik</th>
                        <th>Kepribadian</th>
                        <th>Sosial</th>
                        <th>Profesional</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scores as $s)
                    <tr>
                        <td class="fw-semibold text-start">{{ $s->guru->nama }}</td>
                        <td>{{ $s->competency_scores['pedagogik'] ?? 0 }}</td>
                        <td>{{ $s->competency_scores['kepribadian'] ?? 0 }}</td>
                        <td>{{ $s->competency_scores['sosial'] ?? 0 }}</td>
                        <td>{{ $s->competency_scores['profesional'] ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
                        Nilai akhir merupakan penjumlahan dari kontribusi berbobot setiap role:
                    </p>
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <p class="mb-2 fw-semibold">NILAI AKHIR = KS + RG + WM</p>
                            <p class="mb-0 text-muted small">
                                Dimana KS, RG, dan WM sudah merupakan nilai terbobot (rata-rata × bobot role)
                            </p>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-clipboard-list me-2"></i>3. Nilai Per Kompetensi
                    </h6>
                    <p class="mb-3">
                        Setiap pertanyaan dalam penilaian dikategorikan dalam 4 kompetensi guru:
                    </p>
                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-primary mb-1"><i class="fas fa-chalkboard-teacher me-2"></i>Pedagogik</h6>
                                    <small class="text-muted">Kemampuan dalam mengelola pembelajaran</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-success mb-1"><i class="fas fa-user-check me-2"></i>Kepribadian</h6>
                                    <small class="text-muted">Sikap dan karakter guru</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-info h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-info mb-1"><i class="fas fa-comments me-2"></i>Sosial</h6>
                                    <small class="text-muted">Kemampuan berinteraksi dan berkomunikasi</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-warning h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-warning mb-1"><i class="fas fa-graduation-cap me-2"></i>Profesional</h6>
                                    <small class="text-muted">Penguasaan materi dan pengembangan diri</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mb-0">
                        Nilai per kompetensi dihitung dari rata-rata semua jawaban yang masuk dalam kategori kompetensi tersebut,
                        dari semua role yang menilai.
                    </p>

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
