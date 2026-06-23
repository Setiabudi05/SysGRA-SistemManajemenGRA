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
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}" class="text-muted">Dashboard</a></li>
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
        <div class="card-header border-0 pt-3 bg-transparent">
            <div class="d-flex justify-content-end align-items-center gap-2">
                <select id="filter_status" class="form-select form-select-sm shadow-sm" style="width: 150px;">
                    <option value="">Status...</option>
                    <option value="LUNAS">Lunas</option>
                    <option value="BELUM LUNAS">Belum Lunas</option>
                </select>
                <input type="date" id="filter_tgl" class="form-control form-control-sm shadow-sm" style="width: 140px;">
                <button id="btn_reset" class="btn btn-sm btn-secondary shadow-sm" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        let table = $('#table-pembayaran').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('owner.pembayaran.data') }}",
                data: function (d) {
                    d.status = $('#filter_status').val();
                    d.tgl = $('#filter_tgl').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                { data: 'tanggal_full', name: 'event_date' },
                { data: 'pengantin', name: 'bride_groom_name' },
                { data: 'paket_nama', name: 'paket_nama' },
                { data: 'harga_paket', name: 'harga_paket' },
                { data: 'sisa_tagihan', name: 'sisa_tagihan' },
                { data: 'status_pembayaran', name: 'status_pembayaran', class: 'text-center' },
                { data: 'action', orderable: false, searchable: false, class: 'text-center' }
            ]
        });

        // Trigger filter otomatis
        $('#filter_status, #filter_tgl').on('change', function () {
            table.draw();
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1000 });
            Toast.fire({ icon: 'info', title: 'Memperbarui data...' });
        });

        // Reset Filter
        $('#btn_reset').on('click', function () {
            $('#filter_status').val('');
            $('#filter_tgl').val('');
            table.draw();
        });
    });
</script>
@endpush