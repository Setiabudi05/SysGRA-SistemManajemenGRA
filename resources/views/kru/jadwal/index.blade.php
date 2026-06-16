@extends('layouts.master')
@section('title', 'Jadwal')

@push('css')
    {{-- DataTables CSS 2.0.7 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <style>
        /* Samakan font size dengan dashboard kru */
        #jadwal-table th,
        #jadwal-table td {
            font-size: 0.85rem;
        }

        .filter-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #6c757d;
        }

        .card-header {
            padding: 1.5rem 1.5rem 0 1.5rem;
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #435ebe;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <div class="row align-items-center">
                {{-- Bagian Kiri: Judul & Breadcrumb --}}
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3 class="fw-bold text-dark mb-0">Jadwal Penugasan Saya</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mt-2 mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('kru.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Jadwal</li>
                        </ol>
                    </nav>
                </div>

                {{-- Bagian Kanan: Tombol Cetak PDF --}}
                <div class="col-12 col-md-6 order-md-2 order-first text-md-end mt-3 mt-md-0">
                    <button id="btn-print" class="btn btn-secondary shadow-sm px-3 fw-bold">
                        <i class="bi bi-printer me-1"></i> Cetak Jadwal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <div class="card border-0 shadow-sm">
            {{-- Bagian filter tetap sama seperti sebelumnya --}}
            <div class="card-header bg-transparent border-0 pb-3">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <h5 class="fw-bold mb-0 me-auto">Log Jadwal</h5>
                    {{-- Filter Bulan --}}
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

                    {{-- Filter Tahun --}}
                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-label">Tahun:</span>
                        <select id="filter-tahun" class="form-select form-select-sm shadow-sm" style="width: 100px;">
                            <option value="">Semua</option>
                            @for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body mt-2">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="jadwal-table">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 15%">Tanggal</th>
                                <th style="width: 15%">Nama</th>
                                <th style="width: 20%">Alamat</th>
                                <th style="width: 15%">Paket</th>
                                <th style="width: 15%">Asisten</th>
                                <th style="width: 10%">FG</th>
                                <th style="width: 15%" class="text-center">Aksi</th>
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
            // === KUNCI UTAMA: Tangkap parameter bulan & tahun dari URL (bawaan redirect klik notifikasi) ===
            const urlParams = new URLSearchParams(window.location.search);
            const paramBulan = urlParams.get('bulan');
            const paramTahun = urlParams.get('tahun');

            // Jika parameter ditemukan, paksa value dropdown select mengikuti nilainya sebelum tabel dirender
            if (paramBulan) {
                $('#filter-bulan').val(paramBulan);
            }
            if (paramTahun) {
                $('#filter-tahun').val(paramTahun);
            }

            // Inisialisasi DataTable Jadwal Kru
            let table = $('#jadwal-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('kru.jadwal.data') }}",
                    data: function (d) {
                        // Mengirimkan nilai filter bulan & tahun yang aktif ke server side controller
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
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [[1, 'asc']],
                language: {
                    search: "Cari:",
                    lengthMenu: "_MENU_",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ entri",
                    zeroRecords: "Tidak ada jadwal yang ditemukan"
                }
            });

            // Pemicu redraw tabel saat dropdown diubah manual oleh kru
            $('#filter-bulan, #filter-tahun').change(function () {
                table.draw();
            });

            // Fitur cetak pdf sesuai filter
            $(document).on('click', '#btn-print', function () {
                const url = "{{ route('kru.jadwal.print') }}?bulan=" + $('#filter-bulan').val() + "&tahun=" + $('#filter-tahun').val();
                window.open(url, '_blank');
            });
        });
    </script>
@endpush