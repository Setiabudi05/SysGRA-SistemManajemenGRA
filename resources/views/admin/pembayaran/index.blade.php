@extends('layouts.master')
@section('title', 'Manajemen Pembayaran - Griya Rias Asmara')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
{{-- Menggunakan CSS Global Admin SysGRA --}}
<link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-7">
                {{-- Navigasi Breadcrumb Sejajar --}}
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Pembayaran</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Kelola Pembayaran & Cicilan</h3>
                <p class="text-muted mb-0 small">Pantau arus kas masuk mulai dari DP hingga pelunasan.</p>
            </div>

            <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center mt-3 mt-md-0">
                <a href="{{ route('admin.pembayaran.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> Catat Cicilan Baru
                </a>
            </div>
        </div>
    </div>
    <hr>
</div>

<section class="section">
    {{-- Kartu Info Syarat DP minimal --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card bg-light-primary border-0 p-3 shadow-sm">
                <small class="fw-bold text-uppercase text-primary opacity-75" style="font-size: 0.7rem;">Syarat Fiks Booking</small>
                <h5 class="mb-0 fw-bold text-primary">Wajib DP Rp 2.000.000</h5>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="table-pembayaran">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama Pengantin</th>
                            <th>Total Terbayar</th>
                            <th>Sisa Tagihan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="120px">Action</th>
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
        $('#table-pembayaran').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.pembayaran.data') }}",
            lengthMenu: [
                [3, 5, 10, 25],
                [3, 5, 10, 25]
            ],
            pageLength: 5,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    class: 'text-center'
                },
                {
                    data: 'pengantin',
                    name: 'bride_groom_name',
                    class: 'fw-bold'
                },
                {
                    data: 'total_bayar',
                    name: 'total_bayar',
                    render: function(data) {
                        let val = data.replace(/[^0-9]/g, '');
                        let formatted = parseInt(val).toLocaleString('id-ID');
                        // Menghapus mx-auto dan menggunakan padding atau lebar tetap yang sejajar kiri-kanan
                        return `
                <div class="d-flex justify-content-between text-success fw-bold px-2" style="width: 140px;">
                    <span>Rp</span>
                    <span class="text-end">${formatted}</span>
                </div>`;
                    }
                },
                {
                    data: 'sisa',
                    name: 'sisa',
                    render: function(data) {
                        let val = data.replace(/[^0-9]/g, '');
                        let formatted = parseInt(val).toLocaleString('id-ID');
                        return `
                <div class="d-flex justify-content-between text-danger fw-bold px-2" style="width: 140px;">
                    <span>Rp</span>
                    <span class="text-end">${formatted}</span>
                </div>`;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    class: 'text-center'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    class: 'text-center'
                }
            ],
            language: {
                search: "Search:",
                lengthMenu: "_MENU_ &nbsp; entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    next: '<i class="bi bi-chevron-right"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>'
                },
                emptyTable: "Belum ada riwayat pembayaran yang tercatat"
            }
        });
    });
</script>
{{-- TAMBAHKAN KODE INI DI SINI --}}
@if(session('swal_success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('swal_success') }}",
        timer: 2500,
        showConfirmButton: false,
        customClass: {
            popup: 'swal-custom-popup'
        }
    });
</script>
@endif
@endpush