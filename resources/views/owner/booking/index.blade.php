@extends('layouts.master')
@section('title', 'Laporan Pesanan')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-7">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Laporan Pesanan</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Laporan Pesanan Pengantin</h3>
                    <p class="text-muted mb-0">Monitoring data pesanan berdasarkan jadwal operasional.</p>
                </div>

                <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center mt-3 mt-md-0 gap-2">
                    <button id="btn-print" class="btn btn-secondary shadow-sm">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </button>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <h5 class="card-title mb-0 me-auto">Log Pesanan</h5>

                    {{-- Filter Bulan --}}
                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-label text-muted">Bulan:</span>
                        <select id="filter-bulan" class="form-select form-select-sm shadow-sm">
                            <option value="">Semua</option>
                            @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $i => $b)
                                <option value="{{ $b }}" {{ $i + 1 == date('n') ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Tahun --}}
                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-label text-muted">Tahun:</span>
                        <select id="filter-tahun" class="form-select form-select-sm shadow-sm">
                            <option value="">Semua</option>
                            @for ($y = date('Y'); $y <= date('Y') + 5; $y++)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="jadwal-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Tanggal</th> {{-- Kolom Gabungan --}}
                                <th>Nama Pengantin</th>
                                <th>Paket</th>
                                <th>Harga</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            let table = $('#jadwal-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('owner.booking.data') }}",
                    data: function (d) {
                        d.bulan = $('#filter-bulan').val();
                        d.tahun = $('#filter-tahun').val();
                    }
                },
                columns: [
    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
    { 
        data: 'tanggal_full', 
        name: 'tanggal_awal', // Kita arahkan pengurutan database ke kolom asli 'tanggal_awal'
        orderable: true 
    },
    { data: 'nama', name: 'nama', class: 'fw-bold text-primary' },
                    { data: 'paket.nama_paket', name: 'paket.nama_paket', defaultContent: '-' },
                    {
                        data: 'harga_paket',
                        render: function (data) {
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                    },
                    { data: 'status', name: 'status', class: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ]
            });

            $('#filter-bulan, #filter-tahun').change(function () { table.draw(); });
        });
    </script>
@endpush