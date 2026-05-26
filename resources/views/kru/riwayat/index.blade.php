@extends('layouts.master')
@section('title', 'Riwayat Tugas')

@push('css')
    {{-- DataTables CSS 2.0.7 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <style>
        #riwayat-table th,
        #riwayat-table td {
            font-size: 0.85rem;
        }

        .filter-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #6c757d;
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #435ebe;
        }

        .page-heading {
            margin-top: -1.5rem;
        }

        /* Badge Status Selesai */
        .badge-selesai {
            background-color: #e8fadf;
            color: #198754;
            border: 1px solid #198754;
            font-size: 0.7rem;
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row align-items-center">
                {{-- Bagian Kiri: Judul & Breadcrumb --}}
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3 class="fw-bold text-dark mb-0">Riwayat Tugas</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mt-1 mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('kru.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Riwayat</li>
                        </ol>
                    </nav>
                </div>

                {{-- Bagian Kanan: Tombol Cetak --}}
                <div class="col-12 col-md-6 order-md-2 order-first text-md-end mt-3 mt-md-0">
                    <button type="button" id="btn-print-riwayat" class="btn btn-secondary shadow-sm px-3 fw-bold">
                        <i class="bi bi-printer me-1"></i> Cetak Riwayat
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-3">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <h5 class="fw-bold mb-0 me-auto">Log Riwayat Pekerjaan</h5>

                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-label">Bulan:</span>
                        <select id="filter-bulan" class="form-select form-select-sm shadow-sm" style="width: 140px;">
                            <option value="">Semua Bulan</option>
                            @php
                                $bulans = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $currentMonth = $bulans[(int) date('m') - 1];
                            @endphp
                            @foreach ($bulans as $b)
                                <option value="{{ $b }}" {{ $b == $currentMonth ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-label">Tahun:</span>
                        <select id="filter-tahun" class="form-select form-select-sm shadow-sm" style="width: 100px;">
                            @for ($y = date('Y') - 2; $y <= date('Y'); $y++)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body mt-2">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="riwayat-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%">No</th>
                                <th style="width: 15%">Tanggal</th>
                                <th style="width: 15%">Nama</th>
                                <th style="width: 25%">Alamat</th>
                                <th style="width: 15%">Paket</th>
                                <th style="width: 15%">Asisten</th>
                                <th style="width: 10%">FG</th>
                                <th class="text-center" style="width: 10%">Status</th>
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
            // 1. Inisialisasi DataTable
            let table = $('#riwayat-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('kru.riwayat.data') }}",
                    data: function (d) {
                        d.bulan = $('#filter-bulan').val();
                        d.tahun = $('#filter-tahun').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'tanggal_custom', name: 'tanggal_awal' },
                    { data: 'nama', name: 'nama', className: 'fw-bold' },
                    { data: 'alamat', name: 'alamat' },
                    { data: 'nama_paket', name: 'nama_paket' },
                    { data: 'asisten', name: 'asisten' },
                    { data: 'fg', name: 'fg' },
                    { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [], // KUNCI 2: Ubah dari [[1, 'asc']] menjadi [] agar patuh pada controller
                language: {
                    search: "Cari riwayat:",
                    lengthMenu: "_MENU_",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ entri",
                    zeroRecords: "Tidak ada riwayat pekerjaan ditemukan"
                }
            });

            // 2. Filter Handler
            $('#filter-bulan, #filter-tahun').change(function () {
                table.draw();
            });

            // 3. Tombol Cetak (Diletakkan di luar fungsi change agar bisa diklik kapan saja)
            $(document).on('click', '#btn-print-riwayat', function (e) {
                e.preventDefault();
                const bulan = $('#filter-bulan').val();
                const tahun = $('#filter-tahun').val();
                const url = "{{ route('kru.riwayat.print') }}?bulan=" + bulan + "&tahun=" + tahun;

                window.open(url, '_blank');
            });
        });
    </script>
@endpush