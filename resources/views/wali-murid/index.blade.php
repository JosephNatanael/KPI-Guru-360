@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 text-primary fw-bold">Data Wali Murid</h3>
                <p class="text-muted mb-0">Kelola data wali murid dan siswa</p>
            </div>
            <a href="{{ route('wali-murid.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-person-plus-fill me-2"></i>Tambah Wali Murid
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
                                <th>Nama Wali</th>
                                <th>Akun User</th>
                                <th>Nama Anak</th>
                                <th>Kelas</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($waliMurids as $wm)
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="avatar-circle">
                                            {{ strtoupper(substr($wm->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $wm->nama }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($wm->user)
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $wm->user->name }}</span>
                                            <small class="text-muted">{{ $wm->user->email }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Tidak terhubung ke user</span>
                                    @endif
                                </td>
                                <td>{{ $wm->nama_anak }}</td>
                                <td>
                                    <span class="badge bg-custom-light text-dark border">
                                        {{ $wm->kelas }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('wali-murid.edit', $wm->id) }}" class="btn btn-outline-warning btn-icon btn-sm me-1" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    <form action="{{ route('wali-murid.destroy', $wm->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-icon btn-sm btn-delete" data-name="{{ $wm->nama }}" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data wali murid.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-end">
                   {{ $waliMurids->links() }}
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
                    title: 'Hapus Wali Murid?',
                    text: "Menghapus data: " + name,
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
