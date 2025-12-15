@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Dashboard Nilai Akhir 360°</h3>

    @if(!$periode)
        <div class="alert alert-warning">
            Belum ada periode aktif.
        </div>
    @else
        <div class="card">
            <div class="card-header">
                Grafik Nilai Guru – Periode {{ $periode->tahun_ajaran }}
            </div>
            <div class="card-body">
                <canvas id="chartGuru" height="120"></canvas>
            </div>
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
var ctx = document.getElementById('chartGuru').getContext('2d');

var chartGuru = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
            label: "Nilai Akhir 360°",
            data: {!! json_encode($scores) !!},
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
</script>
@endsection
