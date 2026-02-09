@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 text-primary fw-bold">Management User</h3>
                <p class="text-muted mb-0">Kelola data pengguna sistem KPI 360</p>
            </div>
            <a href="{{ route('user.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-person-plus-fill me-2"></i>Tambah User
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
                                <th>Nama Pengguna</th>
                                <th class="d-none d-md-table-cell">Email Address</th>
                                <th>Role Akses</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $u)
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="avatar-circle">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $u->name }}</div>
                                            <small class="text-muted d-block d-md-none">{{ $u->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">{{ $u->email }}</td>
                                <td>
                                    @php
                                        $roleClass = match($u->role) {
                                            'guru' => 'guru',
                                            'kepala_sekolah' => 'kepala_sekolah',
                                            'admin' => 'admin',
                                            'wali_murid' => 'wali_murid',
                                            default => 'secondary'
                                        };
                                        $roleLabel = ucwords(str_replace('_', ' ', $u->role));
                                    @endphp
                                    <span class="badge badge-role {{ $roleClass }}">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('user.edit', $u->id) }}" class="btn btn-outline-warning btn-icon btn-sm me-1" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    <form action="{{ route('user.destroy', $u->id) }}" class="d-inline delete-form" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-icon btn-sm btn-delete" data-name="{{ $u->name }}" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-end">
                   {{ $users->links() }}
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
                    title: 'Hapus User?',
                    text: "Anda yakin ingin menghapus user " + name + "?",
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
