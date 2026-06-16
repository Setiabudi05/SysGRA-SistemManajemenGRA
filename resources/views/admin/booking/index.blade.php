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
        // Deklarasikan variabel table secara global agar bisa dibaca di dalam fungsi hapusBooking
        let table;

        $(document).ready(function () {
            table = $('.bookings-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.booking.data') }}",
                lengthMenu: [
                    [3, 5, 10, 25],
                    [3, 5, 10, 25]
                ],
                pageLength: 5,
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        class: 'text-center'
                    },
                    {
                        data: 'whatsapp_number',
                        name: 'whatsapp_number'
                    },
                    {
                        data: 'pengantin',
                        name: 'bride_groom_name',
                        class: 'fw-bold text-primary'
                    },
                    {
                        data: 'event_date',
                        name: 'event_date',
                        render: function (data) {
                            if (!data || data === '-') return '-';
                            let dateParts = data.split('-');
                            if (dateParts.length === 3) {
                                let dateObj = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
                                return dateObj.toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                });
                            }
                            return data;
                        }
                    },
                    {
                        data: 'package_name',
                        name: 'package_name'
                    },
                    {
                        data: 'package_price',
                        name: 'package_price',
                        render: function (data) {
                            return `<div class="price-align text-dark">${data}</div>`;
                        }
                    },
                    {
                        data: 'sisa_tagihan',
                        name: 'sisa_tagihan',
                        render: function (data) {
                            let colorClass = (data === 'Rp 0' || data === 'Rp 00') ? 'text-success fw-bold' : 'text-danger fw-bold';
                            return `<div class="price-align ${colorClass}">${data}</div>`;
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
                order: [
                    [3, 'desc']
                ],
                language: {
                    search: "Search:",
                    lengthMenu: "_MENU_ &nbsp; entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        next: '<i class="bi bi-chevron-right"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>'
                    },
                    emptyTable: "Belum ada riwayat pesanan yang tercatat"
                }
            });
        });

        // FUNGSI HAPUS BOOKING AMAN (Anti-Mental & Menggunakan Variabel Global Instance)
        function hapusBooking(id) {
            Swal.fire({
                title: "Hapus Pesanan?",
                text: "Data pesanan dan riwayat terkait akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        // Tambahkan kata /destroy/ di tengah jalurnya
                        url: "{{ url('admin/booking/destroy') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (res) {
                            Swal.fire({
                                icon: res.success ? 'success' : 'error',
                                title: res.success ? 'Berhasil!' : 'Gagal!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Gunakan reload via pointer instance DataTables global agar reaktif instan
                            if (res.success && table) {
                                table.ajax.reload(null, false);
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Sistem!',
                                text: 'Terjadi kegagalan komunikasi token CSRF atau endpoint Route.'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush

{{-- URUTAN BENAR: Taruh session flash message di luar block stack @push script --}}
@if(session('swal_success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('swal_success') }}",
            timer: 2500,
            showConfirmButton: false
        });
    </script>
@endif