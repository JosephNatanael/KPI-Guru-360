@extends('layouts.app')


@section('content')
<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 text-primary fw-bold">Pertanyaan KPI</h3>
                <p class="text-muted mb-0">Kelola daftar pertanyaan untuk penilaian</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#copyQuestionsModal">
                    <i class="bi bi-files me-2"></i>Salin dari Periode
                </button>
                <a href="{{ route('kpi-questions.create') }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle-fill me-2"></i>Tambah Pertanyaan
                </a>
            </div>
        </div>

        <!-- Modal Salin Pertanyaan -->
        <div class="modal fade" id="copyQuestionsModal" tabindex="-1" aria-labelledby="copyQuestionsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('kpi-questions.copy') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="copyQuestionsModalLabel">Salin Pertanyaan dari Periode Lain</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning small mb-4">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Perhatian:</strong> Proses ini akan menghapus semua pertanyaan yang sudah ada di periode tujuan dan menggantinya dengan pertanyaan dari periode asal.
                            </div>
                            <p class="text-muted small mb-3">Fitur ini membantu Anda memulai periode baru dengan cepat dengan menyalin struktur pertanyaan dari periode sebelumnya.</p>
                            
                            <div class="mb-3">
                                <label class="form-label">Periode Asal (Sumber)</label>
                                <select name="from_period_id" class="form-select" required>
                                    <option value="">-- Pilih Periode Asal --</option>
                                    @foreach($periods as $p)
                                        @if($p->id != ($activePeriod->id ?? 0))
                                            <option value="{{ $p->id }}">
                                                {{ $p->tahun_ajaran }} ({{ ucfirst($p->semester) }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Periode Tujuan (Target)</label>
                                <input type="text" class="form-control bg-light" value="{{ $activePeriod->tahun_ajaran ?? '-' }} ({{ ucfirst($activePeriod->semester ?? '-') }})" readonly>
                                <input type="hidden" name="to_period_id" value="{{ $activePeriod->id ?? '' }}">
                                <div class="form-text text-info">
                                    <i class="bi bi-info-circle me-1"></i> Pertanyaan akan disalin ke periode aktif saat ini.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Mulai Salin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fading show shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card card-premium mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <form method="GET" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-calendar-event text-muted"></i>
                            </span>
                            <select name="periode_id" class="form-select border-start-0 ps-0 bg-light" onchange="this.form.submit()">
                                @foreach($periods as $p)
                                    <option value="{{ $p->id }}" {{ $periodeId == $p->id ? 'selected' : '' }}>
                                        Periode: {{ $p->tahun_ajaran }} ({{ ucfirst($p->semester) }})
                                        @if($p->status === 'aktif') [AKTIF] @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-filter text-muted"></i>
                            </span>
                            <select name="kpi_id" class="form-select border-start-0 ps-0 bg-light" onchange="this.form.submit()">
                                <option value="">-- Semua KPI --</option>
                                @foreach($indikators as $kpi)
                                    <option value="{{ $kpi->id }}" {{ $kpiId == $kpi->id ? 'selected' : '' }}>
                                        {{ $kpi->nama }} ({{ ucfirst($kpi->kompetensi) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body-premium p-0 mt-3">
                <div class="table-responsive">
                    <table class="table-premium align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 20%;">KPI</th>
                                <th style="width: 15%;">Kompetensi</th>
                                <th style="width: 45%;">Pertanyaan</th>
                                <th style="width: 10%;">Urutan</th>
                                <th class="text-end" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $q)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $q->kpi->nama ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ strtolower($q->kpi->kompetensi ?? 'default') }}">
                                            {{ ucfirst($q->kpi->kompetensi ?? '-') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-wrap">{{ $q->pertanyaan }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $q->urutan }}</span>
                                    </td>
                                    <td class="text-end">
                                        @if($q->period && $q->period->status === 'aktif')
                                            <a href="{{ route('kpi-questions.edit', $q->id) }}" class="btn btn-outline-warning btn-icon btn-sm me-1" data-bs-toggle="tooltip" title="Edit">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <form action="{{ route('kpi-questions.destroy', $q->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger btn-icon btn-sm btn-delete" data-name="Pertanyaan #{{ $q->urutan }}" data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-secondary opacity-75" data-bs-toggle="tooltip" title="Periode sudah dikunci">
                                                <i class="bi bi-lock-fill me-1"></i> Terkunci
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-question-circle display-6 d-block mb-3"></i>
                                        Belum ada pertanyaan KPI yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="text-muted small">
                    Total: <strong>{{ $questions->count() }}</strong> pertanyaan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const name = this.getAttribute('data-name'); // Could be truncated if too long

                Swal.fire({
                    title: 'Hapus Pertanyaan?',
                    text: "Pertanyaan ini akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection








