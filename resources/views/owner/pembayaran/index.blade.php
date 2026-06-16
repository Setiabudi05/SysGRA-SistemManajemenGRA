@extends('layouts.master')
@section('title', 'Laporan Pembayaran')

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
                            <li class="breadcrumb-item active text-primary" aria-current="page">Laporan Pembayaran</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Laporan Pembayaran & Tagihan</h3>
                    <p class="text-muted mb-0 small">Pantau pelunasan berdasarkan jadwal pengantin secara berurutan.</p>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <h5 class="card-title mb-0 me-auto">Log Tagihan</h5>

                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-label text-muted">Bulan:</span>
                        <select id="filter-bulan" class="form-select form-select-sm shadow-sm">
                            <option value="">Semua</option>
                            @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $i => $b)
                                <option value="{{ $b }}" {{ $i + 1 == date('n') ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

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
                    <table class="table table-hover align-middle w-100" id="table-pembayaran">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Nama Pengantin</th>
                                <th>Paket</th>
                                <th>Harga</th>
                                <th>Sisa Tagihan</th>
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
            let table = $('#table-pembayaran').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('owner.pembayaran.data') }}",
                    data: function (d) {
                        d.bulan = $('#filter-bulan').val();
                        d.tahun = $('#filter-tahun').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'tanggal_full', name: 'tanggal_full' },
                    { data: 'pengantin', name: 'pengantin' }, // Harus sama dengan Controller
                    { data: 'paket_nama', name: 'paket_nama' },
                    { data: 'harga_paket', name: 'harga_paket' },
                    { data: 'sisa_tagihan', name: 'sisa_tagihan' },
                    { data: 'status_pembayaran', name: 'status_pembayaran', class: 'text-center' },
                    { data: 'action', name: 'action', class: 'text-center' }
                ]
            });

            // Trigger update saat filter berubah
            $('#filter-bulan, #filter-tahun').change(function () {
                table.draw();
            });
        });
    </script>
@endpush