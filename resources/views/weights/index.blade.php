@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 text-primary fw-bold">Daftar Bobot Evaluator</h3>
                <p class="text-muted mb-0">Atur proporsi penilaian antar role evaluator</p>
            </div>
            <a href="{{ route('weights.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-sliders me-2"></i>Tambah Bobot
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
                                <th>Jenis Guru</th>
                                <th>Kepala Sekolah (%)</th>
                                <th>Rekan Guru (%)</th>
                                <th>Wali Murid (%)</th>
                                <th class="text-end" width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($weights as $weight)
                            <tr>
                                <td class="fw-bold text-dark">{{ ucwords(str_replace('_', ' ', $weight->jenis_guru)) }}</td>
                                <td>{{ $weight->kepala_sekolah }}%</td>
                                <td>{{ $weight->rekan_guru }}%</td>
                                <td>{{ $weight->wali_murid ?? 0 }}%</td>
                                <td class="text-end">
                                    <a href="{{ route('weights.edit', $weight->id) }}" class="btn btn-outline-warning btn-icon btn-sm me-1" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    <form action="{{ route('weights.destroy', $weight->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-icon btn-sm btn-delete" 
                                                data-name="{{ ucwords(str_replace('_', ' ', $weight->jenis_guru)) }}" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data bobot penilai.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                    title: 'Hapus Bobot?',
                    text: "Menghapus bobot untuk: " + name,
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
