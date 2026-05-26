@extends('layouts.master')
@section('title', 'Dashboard Analitik')

@push('css')
<link rel="stylesheet" href="{{ asset('assets-template/scss/iconly.scss') }}">
<style>
    .page-heading { margin-bottom: 0.5rem !important; }
    .stats-icon i { font-size: 1.4rem; color: #fff; display: flex; align-items: center; justify-content: center; }
    .card { border: none; transition: transform 0.2s; border-radius: 12px; }
    .card:hover { transform: translateY(-3px); }
    .bg-success-light { background-color: rgba(25, 135, 84, 0.08); }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.08); }
    .x-small { font-size: 0.75rem; }
    .table thead th { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; background-color: #f9f9f9; }
    .badge-ai { background: linear-gradient(45deg, #435ebe, #55c6e8); color: white; font-size: 0.65rem; padding: 0.2rem 0.5rem; border-radius: 50px; }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-cpu-fill me-2 text-primary"></i>Dashboard Strategis
                    <span class="badge-ai ms-2"><i class="bi bi-stars me-1"></i>AI Powered</span>
                </h3>
                <p class="text-muted small">Analisis data cerdas dan pemantauan real-time SysGRA.</p>
            </div>
        </div>
    </div>
</div>

<div class="page-content mt-3">
    <section class="row">
        {{-- KIRI: Statistik Utama --}}
        <div class="col-12 col-lg-9">
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body px-3 py-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon purple me-3"><i class="bi bi-stack"></i></div>
                                <div>
                                    <h6 class="text-muted x-small font-semibold">PAKET</h6>
                                    <h4 class="font-extrabold mb-0">{{ $totalPaket }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body px-3 py-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon blue me-3"><i class="bi bi-flower1"></i></div>
                                <div>
                                    <h6 class="text-muted x-small font-semibold">DEKORASI</h6>
                                    <h4 class="font-extrabold mb-0">{{ $totalDekorasi }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body px-3 py-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon green me-3"><i class="bi bi-people-fill"></i></div>
                                <div>
                                    <h6 class="text-muted x-small font-semibold">PELANGGAN</h6>
                                    <h4 class="font-extrabold mb-0">{{ $totalPelanggan }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body px-3 py-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon red me-3"><i class="bi bi-calendar-check"></i></div>
                                <div>
                                    <h6 class="text-muted x-small font-semibold">BOOKING</h6>
                                    <h4 class="font-extrabold mb-0">{{ $totalBooking }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik Proyeksi AI --}}
            <div class="row mt-2">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Proyeksi Permintaan vs Riwayat Aktual</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-booking"></div>
                            <div class="alert alert-light-primary d-flex align-items-center mt-3 py-2 border-0">
                                <i class="bi bi-lightbulb-fill me-2 text-warning"></i>
                                <span class="x-small text-primary fw-bold">Prediksi AI: Kenaikan permintaan diperkirakan memuncak pada bulan Juli mendatang.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Aktivitas Terbaru --}}
            <div class="row mt-2">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent d-flex justify-content-between">
                            <h4 class="card-title mb-0">Aktivitas Booking Terbaru</h4>
                            <a href="{{ route('admin.booking.index') }}" class="btn btn-sm btn-light-primary px-3">Semua Data</a>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Pengantin</th>
                                            <th>Paket</th>
                                            <th>Tgl Acara</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentBookings as $rb)
                                        <tr>
                                            <td class="small fw-bold">{{ $rb->bride_groom_name }}</td>
                                            <td class="small">{{ $rb->package_name }}</td>
                                            <td class="small text-muted">{{ \Carbon\Carbon::parse($rb->event_date)->format('d M Y') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-light-{{ $rb->status == 'completed' ? 'success' : ($rb->status == 'confirmed' ? 'primary' : 'warning') }} x-small">
                                                    {{ strtoupper($rb->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: Finansial --}}
        <div class="col-12 col-lg-3">
            <div class="card bg-success-light border-start border-success border-4 shadow-sm mb-3">
                <div class="card-body py-3">
                    <h6 class="text-muted x-small fw-bold text-uppercase">Pendapatan Bersih</h6>
                    <h4 class="text-success fw-bold mb-0">Rp {{ number_format($netIncome, 0, ',', '.') }}</h4>
                </div>
            </div>

            <div class="card bg-warning-light border-start border-warning border-4 shadow-sm mb-3">
                <div class="card-body py-3">
                    <h6 class="text-muted x-small fw-bold text-uppercase">Payment Pending</h6>
                    <h4 class="text-warning fw-bold mb-0">Rp {{ number_format($pendingPayment, 0, ',', '.') }}</h4>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-transparent text-center pb-0">
                    <h5 class="card-title mb-0 text-muted small uppercase">Analisis Margin</h5>
                </div>
                <div class="card-body">
                    <div id="chart-profitability"></div>
                    <div class="mt-3 p-3 rounded-3 shadow-sm bg-body-tertiary">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-stars text-primary me-2"></i>
                            <h6 class="text-primary fw-bold mb-0 small">Insight Cerdas:</h6>
                        </div>
                        <p class="x-small text-body mb-0 opacity-75">Sisa tagihan (Pending Payment) mencapai Rp {{ number_format($pendingPayment, 0, ',', '.') }}, hubungi pelanggan H-7 acara.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
<script src="{{ asset('assets-template/extensions/apexcharts/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var optionsBooking = {
            chart: { type: 'line', height: 320, toolbar: { show: false } },
            stroke: { width: [0, 3], curve: 'smooth', dashArray: [0, 8] },
            series: [
                { name: 'Data Aktual', type: 'column', data: [12, 18, 25, 30, 28, 22, 19, 23, 27, 29, 24, 15], color: '#435ebe' },
                { name: 'Prediksi AI', type: 'line', data: [10, 15, 20, 25, 30, 35, 42, 38, 30, 32, 28, 25], color: '#ff6666' }
            ],
            xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] },
            legend: { position: 'top', horizontalAlign: 'right' }
        };
        new ApexCharts(document.querySelector("#chart-booking"), optionsBooking).render();

        var optionsProfit = {
            chart: { type: 'donut', height: 260 },
            series: [40, 30, 20, 10],
            labels: ['Premium', 'Standar', 'Hemat', 'Lainnya'],
            colors: ['#435ebe', '#55c6e8', '#ffbb33', '#ff6666'],
            legend: { position: 'bottom', fontSize: '10px' },
            plotOptions: { pie: { donut: { size: '75%' } } }
        };
        new ApexCharts(document.querySelector("#chart-profitability"), optionsProfit).render();
    });
</script>
@endpush