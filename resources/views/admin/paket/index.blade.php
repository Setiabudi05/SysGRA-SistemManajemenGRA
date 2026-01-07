@extends('layouts.master')
@section('title', 'Data Paket Pernikahan')

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
                            <li class="breadcrumb-item active text-primary" aria-current="page">Paket</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Data Paket Pernikahan</h3>
                    <p class="text-muted mb-0">Kelola rincian layanan berdasarkan periode tahun pricelist.</p>
                </div>

                <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center mt-3 mt-md-0 gap-2">
                    <select id="filter-tahun" class="form-select shadow-sm" style="width: 130px;">
                        <option value="">Semua Tahun</option>
                        @php 
                            $currentYear = date('Y'); 
                            $targetYear = 2028; 
                        @endphp
                        @for ($i = $targetYear; $i >= ($currentYear - 3); $i--)
                            <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    
                    <a href="{{ route('admin.paket.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Paket
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
                    <table class="table table-hover align-middle pakets-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Nama Paket</th>
                                <th>MakeUp</th>
                                <th>Dekorasi</th>
                                <th>Dokumentasi</th>
                                <th width="15%">Harga</th>
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
        $(document).ready(function () {
            let table = $('.pakets-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.paket.data') }}",
                    data: function (d) { d.tahun = $('#filter-tahun').val(); }
                },
                lengthMenu: [[3,5, 10, 20], [3,5, 10, 20]],
                pageLength: 3,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'nama_paket', name: 'nama_paket', class: 'fw-bold' },
                    { data: 'makeup', name: 'makeup' },
                    { data: 'dekorasi', name: 'dekorasi' },
                    { data: 'dokumentasi', name: 'dokumentasi' },
                    {
                        data: 'harga',
                        name: 'harga',
                        render: function (data) {
                            let formattedPrice = parseInt(data).toLocaleString('id-ID');
                            // Solusi Sejajar: Simbol di kiri, Nominal di kanan, Jarak dibatasi max-width
                            return `
                                <div class="d-flex justify-content-between text-primary fw-bold mx-auto px-1" style="max-width: 120px;">
                                    <span>Rp</span>
                                    <span>${formattedPrice}</span>
                                </div>
                            `;
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ]
            });

            $('#filter-tahun').on('change', function () { table.draw(); });
        });

       function hapus(id) {
    Swal.fire({
        title: "Apakah Anda Yakin?",
        text: "Data Paket akan dihapus!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: '#3085d6',
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
        customClass: { popup: 'swal-custom-popup' }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ url('admin/paket/destroy') }}/" + id,
                type: "DELETE",
                data: { 
                    _token: "{{ csrf_token() }}" 
                },
                success: function (res) {
                    // Alert Sukses/Gagal otomatis hilang dalam 1.5 detik
                    Swal.fire({
                        icon: res.success ? "success" : "error",
                        title: res.success ? "Berhasil!" : "Gagal!",
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false, // Menghilangkan tombol OK
                        customClass: { popup: 'swal-custom-popup' }
                    });

                    // Jika berhasil, refresh tabel tanpa reload halaman
                    if (res.success) {
                        $('.pakets-table').DataTable().ajax.reload(null, false);
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Terjadi kesalahan saat menghapus data.",
                        timer: 1500,
                        showConfirmButton: false
                    });
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