@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 text-primary fw-bold">Management KPI</h3>
                <p class="text-muted mb-0">Kelola indikator penilaian kinerja guru</p>
            </div>
            <a href="{{ route('kpi.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle-fill me-2"></i>Tambah KPI
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fading show shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fading show shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fading show shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Aktif KPI Card -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0">KPI Aktif</h5>
            <div class="bg-white px-3 py-2 rounded shadow-sm border border-light">
                <span class="text-muted small me-2">Total Bobot:</span>
                @php $totalBobot = $activeKpis->sum('bobot'); @endphp
                <span class="fw-bold {{ $totalBobot != 100 ? 'text-danger' : 'text-success' }}">
                    {{ $totalBobot }}%
                </span>
            </div>
        </div>
        <div class="card card-premium mb-4">
            <div class="card-body-premium p-0">
                <div class="table-responsive">
                    <table class="table-premium align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nama KPI</th>
                                <th class="d-none d-sm-table-cell">Kompetensi</th>
                                <th class="d-none d-md-table-cell">Bobot (%)</th>
                                <th class="d-none d-md-table-cell">Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeKpis as $k)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">{{ $k->nama }}</span>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="badge badge-{{ strtolower($k->kompetensi) }}">
                                        {{ ucfirst($k->kompetensi) }}
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-light text-dark border">{{ $k->bobot }}%</span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-success-soft text-success">Aktif</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('kpi.edit', $k->id) }}" class="btn btn-outline-warning btn-icon btn-sm me-1" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    <form action="{{ route('kpi.toggle-status', $k->id) }}" class="d-inline" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary btn-icon btn-sm me-1" 
                                                data-bs-toggle="tooltip" title="Nonaktifkan"
                                                onclick="return confirm('Nonaktifkan KPI ini?')">
                                            <i class="bi bi-toggle-on"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('kpi.destroy', $k->id) }}" class="d-inline delete-form" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-icon btn-sm btn-delete" data-name="{{ $k->nama }}" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-clipboard-x display-6 d-block mb-2"></i>
                                    Tidak ada KPI Aktif.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Nonaktif KPI Card -->
        <h5 class="fw-bold text-muted mb-3">KPI Nonaktif</h5>
        <div class="card card-premium mb-4 opacity-75">
            <div class="card-body-premium p-0">
                <div class="table-responsive">
                    <table class="table-premium align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nama KPI</th>
                                <th class="d-none d-sm-table-cell">Kompetensi</th>
                                <th class="d-none d-md-table-cell">Bobot (%)</th>
                                <th class="d-none d-md-table-cell">Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inactiveKpis as $k)
                            <tr>
                                <td>{{ $k->nama }}</td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="badge badge-{{ strtolower($k->kompetensi) }}">
                                        {{ ucfirst($k->kompetensi) }}
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">{{ $k->bobot }}%</td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-secondary-soft text-secondary">Nonaktif</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('kpi.edit', $k->id) }}" class="btn btn-outline-warning btn-icon btn-sm me-1" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    <form action="{{ route('kpi.toggle-status', $k->id) }}" class="d-inline" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-icon btn-sm me-1" 
                                                data-bs-toggle="tooltip" title="Aktifkan"
                                                onclick="return confirm('Aktifkan KPI ini?')">
                                            <i class="bi bi-toggle-off"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('kpi.destroy', $k->id) }}" class="d-inline delete-form" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-icon btn-sm btn-delete" data-name="{{ $k->nama }}" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Tidak ada KPI Nonaktif.
                                </td>
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
        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const name = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Hapus KPI?',
                    text: "Menghapus KPI: " + name,
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
