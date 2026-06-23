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
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-3 bg-transparent">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <select id="filter_status" class="form-select form-select-sm shadow-sm" style="width: 140px;">
                        <option value="">Status...</option>
                        <option value="LUNAS">Lunas</option>
                        <option value="CONFIRMED">Confirmed</option>
                        <option value="PENDING">Pending</option>
                    </select>
                    <input type="date" id="filter_tgl" class="form-control form-control-sm shadow-sm" style="width: 140px;">
                    <button id="btn_reset" class="btn btn-sm btn-secondary shadow-sm" title="Reset Filter"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-pembayaran" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tgl Acara</th>
                                <th>Pengantin</th>
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
                    d.status = $('#filter_status').val();
                    d.tgl = $('#filter_tgl').val();
                },
                error: function(xhr, error, code) {
                    console.log(xhr.responseText); // INI AKAN MENUNJUKKAN ERROR ASLINYA DI CONSOLE
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal_acara', name: 'tanggal_acara' },
                { data: 'pengantin', name: 'pengantin' },
                { data: 'total_bayar', name: 'total_bayar' },
                { data: 'sisa', name: 'sisa' },
                { data: 'status', name: 'status', class: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
            ]
        });

        $('#filter_status, #filter_tgl').on('change', function() {
            table.draw();
        });

        $('#btn_reset').on('click', function() {
            $('#filter_status, #filter_tgl').val('');
            table.draw();
        });
    });
</script>

@if(session('swal_success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('swal_success') }}",
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif
@endpush