@extends('layouts.master')
@section('title', 'Dashboard Owner')

@push('css')
    <style>
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .font-extrabold {
            font-weight: 800;
        }

        .stats-card {
            min-height: 120px;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                {{-- Breadcrumb Navigasi --}}
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Home</a>
                        </li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Dashboard</li>
                    </ol>
                </nav>

                {{-- Judul dan Sapaan --}}
                <h3 class="fw-bold mb-0">Dashboard Strategis</h3>
                <p class="text-muted small">
                    <i class="bi bi-person-circle me-1"></i> Selamat datang kembali,
                    {{ auth()->user()->name ?? 'Administrator' }}
                </p>
            </div>
        </div>
    </div>

    <div class="page-content">
        {{-- STATISTIK CARD --}}
        <div class="row">
            <div class="col-6 col-md-3">
                <div class="card shadow-sm stats-card">
                    <div class="card-body">
                        <h6 class="text-muted">Tingkat Okupansi</h6>
                        <h4 class="font-extrabold">{{ number_format($occupancyRate, 1) }}%</h4>
                        <div class="progress mt-2" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ $occupancyRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm stats-card">
                    <div class="card-body">
                        <h6 class="text-muted">Pesanan Aktif</h6>
                        <h4 class="font-extrabold">{{ $pesananAktif }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm stats-card">
                    <div class="card-body">
                        <h6 class="text-muted">Laba Bersih</h6>
                        <h4 class="font-extrabold text-success">Rp {{ number_format($netProfit, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm stats-card">
                    <div class="card-body">
                        <h6 class="text-muted">Piutang Tertunda</h6>
                        <h4 class="font-extrabold text-warning">Rp {{ number_format($pendingReceivables, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK TREN PENDAPATAN --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title">Tren Pendapatan Bulanan</h5>
                    </div>
                    <div class="card-body">
                        <div id="chart-pendapatan"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var options = {
            chart: { type: 'area', height: 350 },
            series: [{
                name: 'Pendapatan',
                data: [30000000, 40000000, 35000000, {{ $netProfit }}]
            }],
            xaxis: { categories: ['Maret', 'April', 'Mei', 'Juni'] },
            colors: ['#435ebe'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth' }
        };
        var chart = new ApexCharts(document.querySelector("#chart-pendapatan"), options);
        chart.render();
    </script>
@endpush