@extends('layouts.master')
@section('title', 'Kelola Data User')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <style>
        /* Penyesuaian tinggi select filter agar seimbang dengan tombol utama */
        .filter-select {
            height: 38px;
            font-size: 0.9rem;
            min-width: 210px;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                {{-- Sisi Kiri: Navigasi dan Judul Menu --}}
                <div class="col-12 col-md-5">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">User</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Kelola Data User</h3>
                    <p class="text-muted mb-0 small">Manajemen akun pengguna dan hak akses sistem.</p>
                </div>

                {{-- Sisi Kanan: Dropdown Filter diletakkan di samping tombol Tambah User --}}
                <div class="col-12 col-md-7 d-flex justify-content-md-end align-items-center gap-2 mt-3 mt-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold small text-muted text-nowrap d-none d-sm-inline">Filter:</span>
                        <select id="filter-category" class="form-select filter-select shadow-sm">
                            <option value="">-- Semua Kategori Akun --</option>
                            <option value="karyawan">Karyawan (Admin & Kru)</option>
                            <option value="pelanggan">User (Pelanggan)</option>
                        </select>

                    </div>

                    <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center mt-3 mt-md-0 gap-2">
                        <a href="{{ route('owner.users.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                            <i class="bi bi-person-plus-fill me-1"></i> Tambah User
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle users-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th width="15%">Jabatan</th>
                                <th width="12%">Role</th>
                                <th class="text-center" width="180px">Action</th>
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
            let table = $('.users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('owner.users.data') }}",
                    data: function (d) {
                        d.category = $('#filter-category').val();
                    }
                },
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                pageLength: 10,

                // KUNCI UTAMA: Mengaktifkan panah sortir default DataTables pada kolom Nama (kolom urutan ke-2 / index 1)
                order: [[1, 'asc']],

                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'name', name: 'name', className: 'fw-bold' },
                    { data: 'email', name: 'email' },

                    // TAMBAH KOLOM JABATAN (BARU)
                    {
                        data: 'jabatan',
                        name: 'jabatan',
                        render: function (data) {
                            return '' + data + '</span>';
                        }
                    },
                    {
                        data: 'role',
                        name: 'role',
                        render: function (data) {
                            return '' + data + '</span>';
                        }
                    },

                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });
            // Re-draw tabel secara instan saat filter dropdown dirubah nilainya
            $('#filter-category').on('change', function () {
                table.ajax.reload(null, false);
            });
        });

        // FUNGSI AJAX DELETE DENGAN SWEETALERT2
        function hapusUser(id) {
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Akun user ini akan dihapus permanen dari basis data!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('owner/users') }}/" + id,
                        type: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $('.users-table').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: res.message,
                                    confirmButtonColor: '#435ebe'
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan sistem internal.",
                                confirmButtonColor: '#435ebe'
                            });
                        }
                    });
                }
            });
        }
    </script>

    {{-- Notifikasi SweetAlert flash session dari Controller --}}
    @if(session('swal_success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('swal_success') }}",
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        </script>
    @endif
@endpush