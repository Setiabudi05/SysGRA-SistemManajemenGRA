@extends('layouts.master')
@section('title', 'Data Dekorasi')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row align-items-center">
                <div class="col-12 col-md-7">
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Dekorasi</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-1">Data Dekorasi</h3>
                    <p class="text-muted mb-0">Kelola daftar paket dan visualisasi dekorasi pernikahan.</p>
                </div>

                <div class="col-12 col-md-5 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.dekorasi.create') }}" class="btn btn-primary shadow-sm px-4 py-2 fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Dekorasi
                    </a>
                </div>
            </div>
        </div>
        <hr class="mb-3" style="opacity: 0.1;">
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="dekorasi-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Paket</th>
                                <th>Deskripsi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @include('sweetalert::alert')
@endsection

@push('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $('#dekorasi-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.dekorasi.data') }}",
                lengthMenu: [[3, 5, 10, 20, 30], [3, 5, 10, 20, 30]],
                pageLength: 3,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    {
                        data: 'foto', name: 'foto', orderable: false, searchable: false, render: function (data) {
                            if (data) {
                                return '<div class="img-container shadow-sm">' +
                                    '<img src="{{ asset("storage") }}/' + data + '" alt="Foto Dekorasi">' +
                                    '</div>';
                            }
                            return '<div class="img-container bg-light d-flex align-items-center justify-content-center text-muted">No Image</div>';
                        }
                    },
                    { data: 'paket', name: 'paket', className: 'fw-bold' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    // PERBAIKAN: Gunakan className agar selaras dengan header 'text-center'
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });
        });

        function hapusDekorasi(id) {
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Data Dekorasi akan dihapus!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    popup: 'swal-custom-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/dekorasi/destroy') }}/" + id,
                        type: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (res) {
                            Swal.fire({
                                icon: res.success ? "success" : "error",
                                title: res.success ? "Berhasil!" : "Gagal!",
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            if (res.success) {
                                $('#dekorasi-table').DataTable().ajax.reload(null, false);
                            }
                        },
                        error: function () {
                            Swal.fire("Error!", "Terjadi kesalahan saat menghapus data.", "error");
                        }
                    });
                }
            });
        }
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