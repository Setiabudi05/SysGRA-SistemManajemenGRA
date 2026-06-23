@extends('layouts.master')
@section('title', 'Manajemen Pesanan')

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
                    {{-- Breadcrumb disamakan dengan fitur Paket --}}
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Pesanan</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Manajemen Pesanan</h3>
                    <p class="text-muted mb-0 small">Kelola rincian pesanan dan pantau status tagihan pengantin.</p>
                </div>
                <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center mt-3 mt-md-0">
                    <a href="{{ route('admin.booking.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Pesanan
                    </a>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 pt-3 bg-transparent">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <select id="filter-status" class="form-select form-select-sm shadow-sm" style="width: 140px;">
                        <option value="">Status...</option>
                        <option value="PENDING">Pending</option>
                        <option value="CONFIRMED">Confirmed</option>
                        <option value="COMPLETED">Completed</option>
                        <option value="DRAFT">Draft</option>
                    </select>

                    <input type="date" id="tgl-acara" class="form-control form-control-sm shadow-sm" style="width: 140px;"
                        title="Pilih Tanggal">

                    <button id="btn-reset" class="btn btn-sm btn-secondary shadow-sm" title="Reset Filter">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle bookings-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>WhatsApp</th>
                                <th>Pengantin</th>
                                <th>Tgl Acara</th>
                                <th>Paket</th>
                                <th>Harga</th>
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
        let table;
        $(document).ready(function () {
            // Inisialisasi DataTable
            table = $('.bookings-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.booking.data') }}",
                    data: function (d) {
                        d.status = $('#filter-status').val();
                        d.tgl_acara = $('#tgl-acara').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'whatsapp_number', name: 'whatsapp_number' },
                    { data: 'bride_groom_name', name: 'bride_groom_name', class: 'fw-bold text-primary' },
                    { data: 'event_date', name: 'event_date' },
                    { data: 'package_name', name: 'package_name' },
                    { data: 'package_price', name: 'package_price' },
                    { data: 'sisa_tagihan', name: 'sisa_tagihan' },
                    { data: 'status', name: 'status', class: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                order: [[3, 'desc']],
                language: {
                    emptyTable: "Belum ada pesanan yang tercatat"
                }
            });

            // Event Trigger: Filter otomatis saat ada perubahan
            $('#filter-status, #tgl-acara').on('change', function () {
                table.draw();
            });

            // Event Trigger: Reset Filter
            $('#btn-reset').on('click', function () {
                $('#filter-status').val('');
                $('#tgl-acara').val('');
                table.draw();
            });
        });

        // Fungsi Hapus Pesanan
        function hapusBooking(id) {
            Swal.fire({
                title: "Hapus Pesanan?",
                text: "Data pesanan akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/booking/destroy') }}/" + id,
                        type: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (res) {
                            Swal.fire({
                                icon: res.success ? 'success' : 'error',
                                title: res.success ? 'Berhasil!' : 'Gagal!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            if (res.success) table.ajax.reload(null, false);
                        }
                    });
                }
            });
        }
    </script>

    {{-- Notifikasi Sukses Redirect --}}
    @if(session('swal_success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('swal_success') }}",
                timer: 1500,
                showConfirmButton: false
            });
        </script>
    @endif
@endpush