@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 text-primary fw-bold">Master Rekomendasi</h3>
                <p class="text-muted mb-0">Atur kriteria rekomendasi berdasarkan range nilai</p>
            </div>
            <a href="{{ route('recommendations.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-star-fill me-2"></i>Tambah Rekomendasi
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fading show shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card card-premium mb-4">
            <div class="card-body-premium p-0">
                <div class="table-responsive">
                    <table class="table-premium align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nama Kriteria</th>
                                <th>Range Nilai</th>
                                <th>Keterangan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recommendations as $rec)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $rec->nama }}</td>
                                    <td>
                                        <span class="badge bg-custom-light text-primary border border-primary-subtle">
                                            {{ $rec->min_score }} - {{ $rec->max_score }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $rec->keterangan }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('recommendations.edit', $rec->id) }}" class="btn btn-outline-warning btn-icon btn-sm me-1" data-bs-toggle="tooltip" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('recommendations.destroy', $rec->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-outline-danger btn-icon btn-sm btn-delete" data-name="{{ $rec->nama }}" data-bs-toggle="tooltip" title="Hapus">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada data rekomendasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-end">
                   {{ $recommendations->links() }}
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
                    title: 'Hapus Rekomendasi?',
                    text: "Menghapus rekomendasi: " + name,
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
