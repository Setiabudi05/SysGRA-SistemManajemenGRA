@extends('layouts.master')
@section('title', 'Data Dekorasi')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <style>
        /* PERBAIKAN UKURAN FOTO: Dibuat lebih besar, proporsional, dan estetik */
        #dekorasi-table .img-container {
            width: 130px;
            /* Lebar foto dinaikkan */
            height: 95px;
            /* Tinggi foto disesuaikan proporsional */
            overflow: hidden;
            border-radius: 8px;
            /* Membulatkan sudut frame agar modern */
            background-color: #f8f9fa;
            display: inline-block;
            vertical-align: middle;
            border: 1px solid #e2e8f0;
        }

        #dekorasi-table .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Memastikan foto terpotong rapi di tengah tanpa melar */
            object-position: center;
        }

        /* Memberikan sedikit ruang vertikal ekstra pada baris tabel agar tidak sesak */
        #dekorasi-table tbody td {
            padding-top: 12px !important;
            padding-bottom: 12px !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row align-items-center">
                {{-- Sisi Kiri: Judul & Breadcrumb --}}
                <div class="col-12 col-md-7">
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Dekorasi</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-1">Data Dekorasi</h3>
                    <p class="text-muted mb-0 small">Kelola daftar paket dan visualisasi dekorasi pernikahan.</p>
                </div>

                {{-- Sisi Kanan Atas: Hanya Tombol Cetak & Tambah (Bersih) --}}
                <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center mt-3 mt-md-0 gap-2">
                    <button id="btn-print" class="btn btn-secondary shadow-sm fw-bold">
                        <i class="bi bi-printer me-1"></i> Cetak Laporan
                    </button>
                    <a href="{{ route('admin.dekorasi.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Dekorasi
                    </a>
                </div>
            </div>
        </div>
        <hr class="mb-3" style="opacity: 0.1;">
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            {{-- PERBAIKAN STRUKTUR: Dropdown ditaruh di Card Header, Sejajar kanan atas Search --}}
            <div class="card-header bg-transparent border-0 pb-0 pt-3">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <h5 class="card-title mb-0 me-auto">Log Dekorasi</h5>

                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-label text-muted small fw-bold">Paket:</span>
                        <select id="filter-paket" class="form-select form-select-sm shadow-sm"
                            style="width: 160px; cursor: pointer;">
                            <option value="">Semua</option>
                            @foreach($pakets as $paket)
                                <option value="{{ $paket->id }}">{{ $paket->nama_paket }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="dekorasi-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 8%">No</th>
                                <th style="width: 20%">Foto</th>
                                <th style="width: 22%">Paket</th>
                                <th style="width: 35%">Deskripsi Detail</th>
                                <th class="text-center" style="width: 15%">Action</th>
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

    <script>
        $(document).ready(function () {
            // Inisialisasi tabel dengan let agar sama persis sperti modul jadwal
            let table = $('#dekorasi-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.dekorasi.data') }}",
                    data: function (d) {
                        d.paket_id = $('#filter-paket').val();
                    }
                },
                lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                pageLength: 10,
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
                    // KUNCI PERBAIKAN: name diubah menjadi paket.nama_paket agar bisa disearch!
                    { data: 'paket', name: 'paket.nama_paket', className: 'fw-bold' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });

            // Re-draw saat dropdown berubah
            $('#filter-paket').change(function () {
                table.draw();
            });

            // Sinkronisasi aksi cetak laporan
            $('#btn-print').on('click', function (e) {
                e.preventDefault();
                let url = "{{ route('admin.dekorasi.print') }}?paket_id=" + $('#filter-paket').val();
                window.open(url, "_blank");
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
                customClass: { popup: 'swal-custom-popup' }
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

    @if(session('swal_success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('swal_success') }}",
                timer: 2500,
                showConfirmButton: false,
                customClass: { popup: 'swal-custom-popup' }
            });
        </script>
    @endif
@endpush