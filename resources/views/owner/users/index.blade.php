@extends('layouts.master')
@section('title', 'Kelola Data User')

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
                            <li class="breadcrumb-item active text-primary" aria-current="page">User</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Kelola Data User</h3>
                    <p class="text-muted mb-0">Manajemen akun pengguna dan hak akses sistem.</p>
                </div>

                <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center mt-3 mt-md-0 gap-2">
                    <a href="{{ route('owner.users.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah User
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
                    <table class="table table-hover align-middle users-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
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
                },
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                pageLength: 10,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'name', name: 'name', className: 'fw-bold' },
                    { data: 'email', name: 'email' },
                    {
                        data: 'role',
                        name: 'role',
                        render: function (data) {
                            return data.toUpperCase();
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });
        });

        // FUNGSI HAPUS DENGAN TIMER
        function hapusUser(id) {
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Akun user ini akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: { popup: 'swal-custom-popup' }
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
                                // Alert untuk proteksi (misal: hapus diri sendiri)
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
                                text: xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan sistem.",
                                confirmButtonColor: '#435ebe'
                            });
                        }
                    });
                }
            });
        }
    </script>

    {{-- Notifikasi Redirect (Tambah/Edit) DENGAN TIMER --}}
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