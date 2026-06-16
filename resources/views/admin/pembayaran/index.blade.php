@extends('layouts.master')
@section('title', 'Manajemen Pembayaran - Griya Rias Asmara')

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
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Pembayaran</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Kelola Pembayaran & Cicilan</h3>
            </div>
            <div class="col-12 col-md-5 d-flex justify-content-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.pembayaran.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> Catat Cicilan Baru
                </a>
            </div>
        </div>
    </div>
    <hr>
</div>

<section class="section">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="fw-bold">Bulan Acara:</label>
                    <select id="filter_bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold">Tahun Acara:</label>
                    <select id="filter_tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach(range(2025, date('Y') + 1) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="btn_filter" class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-hover align-middle w-100" id="table-pembayaran">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tgl Acara</th>
                        <th>Nama Pengantin</th>
                        <th>Total Terbayar</th>
                        <th>Sisa Tagihan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@push('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        var table = $('#table-pembayaran').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.pembayaran.data') }}",
                data: function(d) {
                    d.bulan = $('#filter_bulan').val();
                    d.tahun = $('#filter_tahun').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal_acara', name: 'event_date' },
                { data: 'pengantin', name: 'bride_groom_name' },
                { data: 'total_bayar', name: 'total_bayar' },
                { data: 'sisa', name: 'sisa' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#btn_filter').click(function() {
            table.ajax.reload();
        });
    });
</script>
@endpush