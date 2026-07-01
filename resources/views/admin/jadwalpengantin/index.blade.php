@extends('layouts.master')
@section('title', 'Data Jadwal Pengantin')

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
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Jadwal Pengantin</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Data Jadwal Pengantin</h3>
                    <p class="text-muted mb-0 small">Kelola rincian jadwal operasional dan penugasan tim.</p>
                </div>

                <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center mt-3 mt-md-0 gap-2">
                    <button id="btn-print" class="btn btn-secondary shadow-sm">
                        <i class="bi bi-printer"></i> Cetak Jadwal
                    </button>
                    <a href="{{ route('admin.jadwalpengantin.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                        <i class="bi bi-plus-lg"></i> Tambah Jadwal
                    </a>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <h5 class="card-title mb-0 me-auto">Log Jadwal</h5>

                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-label text-muted">Tanggal:</span>
                        <input type="date" id="filter-tanggal" class="form-control form-control-sm shadow-sm"
                            style="width: 150px;">
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-label text-muted">Bulan:</span>
                        <select id="filter-bulan" class="form-select form-select-sm shadow-sm">
                            <option value="">Semua</option>
                            @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $i => $b)
                                <option value="{{ $b }}" {{ $i + 1 == date('n') ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-label text-muted">Tahun:</span>
                        <select id="filter-tahun" class="form-select form-select-sm shadow-sm">
                            <option value="">Semua</option>
                            @for ($y = date('Y'); $y <= date('Y') + 5; $y++)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="jadwal-table">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Paket</th>
                                <th>Asisten</th>
                                <th>FG</th>
                                <th>Layos</th>
                                <th>Keterangan</th>
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
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('bulan')) $('#filter-bulan').val(urlParams.get('bulan'));
            if (urlParams.get('tahun')) $('#filter-tahun').val(urlParams.get('tahun'));

            let table = $('#jadwal-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.jadwalpengantin.data') }}",
                    data: function (d) {
                        d.bulan = $('#filter-bulan').val();
                        d.tahun = $('#filter-tahun').val();
                        d.tanggal = $('#filter-tanggal').val(); // Kirim ke server
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'tanggal_full', name: 'tanggal_awal' },
                    { data: 'nama', name: 'nama', class: 'fw-bold text-primary' },
                    { data: 'alamat', name: 'alamat' },
                    { data: 'paket.nama_paket', name: 'paket.nama_paket', defaultContent: '<span class="text-muted small">Tanpa Paket</span>' },
                    { data: 'asisten', name: 'asisten' },
                    { data: 'fg', name: 'fg' },
                    { data: 'layos', name: 'layos' },
                    { data: 'keterangan_text', name: 'keterangan' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ]
            });

            // Update table saat filter berubah
            $('#filter-bulan, #filter-tahun, #filter-tanggal').on('change', function () {
                table.draw();
            });
            $('#btn-print').on('click', function (e) {
                e.preventDefault();
                let url = "{{ route('admin.jadwalpengantin.print') }}?bulan=" + $('#filter-bulan').val() + "&tahun=" + $('#filter-tahun').val();
                window.open(url, "_blank");
            });
        });

        function hapusJadwal(id) {
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Data Jadwal akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/jadwalpengantin/destroy') }}/" + id,
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
                            if (res.success) table.ajax.reload(null, false);
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
                customClass: {
                    popup: 'swal-custom-popup'
                }
            });
        </script>
    @endif
@endpush