@extends('layouts.master')
@section('title', 'Monitor Jadwal Dekor')

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
                            <li class="breadcrumb-item active text-primary" aria-current="page">Monitor Jadwal Dekorasi</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Laporan Jadwal Dekorasi</h3>
                    <p class="text-muted mb-0 small">Pantau logistik dan rincian dekorasi lapangan secara real-time.</p>
                </div>
                <div class="col-12 col-md-5 d-flex justify-content-md-end mt-3 mt-md-0">
                    <button id="btn-print" class="btn btn-secondary shadow-sm px-3 fw-bold">
                        <i class="bi bi-printer"></i> Cetak PDF
                    </button>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div
                class="card-header bg-transparent border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="card-title mb-0">Log Visual Dekorasi</h5>
                <div class="d-flex gap-2">
                    <select id="filter-bulan" class="form-select form-select-sm shadow-sm" style="width: 140px;">
                        <option value="">Semua Bulan</option>
                        @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $b)
                            <option value="{{ $b }}" {{ $b == date('F') ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                    <select id="filter-tahun" class="form-select form-select-sm shadow-sm" style="width: 100px;">
                        @for ($y = date('Y'); $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="jadwal-table">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Nama Pengantin</th>
                                <th>Paket</th>
                                <th class="text-center">Foto</th>
                                <th>Deskripsi/Logistik</th>
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

    <script>
        $(document).ready(function () {
            // 1. Definisikan Nama Bulan dan Waktu Sekarang
            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            let now = new Date();

            // 2. Ambil parameter dari URL browser (jika ada)
            const urlParams = new URLSearchParams(window.location.search);
            const getBulan = urlParams.get('bulan');
            const getTahun = urlParams.get('tahun');

            /**
             * LOGIKA PRIORITAS FILTER:
             * 1. Jika ada parameter di URL, gunakan itu.
             * 2. Jika baru buka fitur (URL bersih), set otomatis ke bulan & tahun sekarang.
             */
            if (getBulan) {
                $('#filter-bulan').val(getBulan);
            } else {
                // Otomatis pilih bulan sekarang agar tidak muncul "Semua Bulan" di awal
                $('#filter-bulan').val(monthNames[now.getMonth()]);
            }

            if (getTahun) {
                $('#filter-tahun').val(getTahun);
            } else {
                // Otomatis pilih tahun sekarang
                $('#filter-tahun').val(now.getFullYear());
            }

            // 3. Inisialisasi DataTable
            let table = $('#jadwal-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('owner.jadwaldekor.data') }}",
                    data: function (d) {
                        // Mengirimkan filter yang aktif ke server
                        d.bulan = $('#filter-bulan').val();
                        d.tahun = $('#filter-tahun').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'tanggal_full', name: 'tanggal_awal' },
                    { data: 'nama', name: 'nama', class: 'fw-bold text-primary' },
                    { data: 'paket_nama', name: 'paket_nama' },
                    { data: 'foto', name: 'foto', orderable: false, searchable: false },
                    { data: 'deskripsi', name: 'deskripsi' }
                ]
            });

            // Reload tabel saat filter dropdown diubah manual
            $('#filter-bulan, #filter-tahun').change(() => table.draw());

        $('#btn-print').click(function () {
            let url = "{{ route('owner.jadwaldekor.print') }}?bulan=" + $('#filter-bulan').val() + "&tahun=" + $('#filter-tahun').val();
            window.open(url, '_blank');
        });
        });
    </script>
@endpush