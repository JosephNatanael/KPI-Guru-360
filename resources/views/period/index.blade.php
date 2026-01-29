@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 text-primary fw-bold">Periode Penilaian</h3>
                <p class="text-muted mb-0">Kelola periode penilaian KPI Guru</p>
            </div>
            <a href="{{ route('period.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-calendar-plus-fill me-2"></i>Tambah Periode
            </a>
        </div>

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

        @if($activePeriod)
            <div class="card card-premium mb-4 border-start border-4 border-success">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="bi bi-calendar-check-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">Periode Aktif Saat Ini</h6>
                            <h4 class="mb-0 text-dark fw-bold">
                                {{ $activePeriod->tahun_ajaran }} - {{ ucfirst($activePeriod->semester) }}
                            </h4>
                        </div>
                        <div class="ms-auto text-end d-none d-md-block">
                            <span class="badge bg-success rounded-pill px-3">
                                <i class="bi bi-check-circle-fill me-1"></i> Sedang Berlangsung
                            </span>
                            <p class="text-muted small mb-0 mt-1">
                                {{ \Carbon\Carbon::parse($activePeriod->tanggal_mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($activePeriod->tanggal_selesai)->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning border-0 shadow-sm mb-4">
                <i class="bi bi-exclamation-circle-fill me-2"></i> Belum ada periode yang diaktifkan. Silakan aktifkan salah satu periode di bawah.
            </div>
        @endif

        <div class="card card-premium mb-4">
            <div class="card-body-premium p-0">
                <div class="table-responsive">
                    <table class="table-premium align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Tahun Ajaran</th>
                                <th>Semester</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($periods as $p)
                            <tr>
                                <td class="fw-bold text-dark">{{ $p->tahun_ajaran }}</td>
                                <td>{{ ucfirst($p->semester) }}</td>
                                <td>{{ $p->tanggal_mulai }}</td>
                                <td>{{ $p->tanggal_selesai }}</td>
                                <td>
                                    @if($p->status === 'aktif')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">
                                            Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('period.edit', $p->id) }}" class="btn btn-outline-warning btn-icon btn-sm me-1" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    
                                    @if($p->status !== 'aktif')
                                    <form action="{{ route('period.destroy', $p->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-icon btn-sm btn-delete" 
                                                data-name="{{ $p->tahun_ajaran }} ({{ $p->semester }})" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-end">
                   {{ $periods->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
         // Initialize Tooltips
         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const name = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Hapus Periode?',
                    text: "Menghapus periode: " + name,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'card-premium',
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    }
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
