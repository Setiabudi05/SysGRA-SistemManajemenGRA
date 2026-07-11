@extends('layouts.master')
@section('title', 'Dashboard Kru')

@push('css')
    {{-- DataTables CSS 2.0.7 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <style>
        .welcome-card {
            background: linear-gradient(135deg, #435ebe 0%, #2e44a1 100%);
            border-radius: 15px;
            color: white;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .welcome-card i.bi-briefcase {
            position: absolute;
            right: 30px;
            top: 20%;
            font-size: 80px;
            color: rgb(255, 255, 255);
        }

        .stats-card {
            border-radius: 12px;
            border: none;
        }

        <style>.welcome-card {
            background: linear-gradient(135deg, #435ebe 0%, #2e44a1 100%);
            border-radius: 15px;
            padding: 30px;
            position: relative;
            overflow: hidden;
            color: #ffffff !important;
            /* Memaksa semua teks di dalamnya jadi putih */
        }

        .welcome-card h2,
        .welcome-card p {
            color: #ffffff !important;
        }

        .welcome-card p {
            opacity: 0.9;
            /* Supaya teks deskripsi tidak terlalu mencolok dibanding nama */
        }

        .welcome-card i.bi-briefcase {
            position: absolute;
            right: 30px;
            top: 20%;
            font-size: 80px;
            color: rgba(255, 255, 255, 0.15);
            /* Ikon tas di background agak diperjelas dikit */
        }

        /* Membatasi lebar kolom agar tidak melar */
        #tableJadwalKru th,
        #tableJadwalKru td {
            font-size: 0.85rem;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        /* Menghilangkan padding atas pada section agar lebih rapat */
        .section.mt-4 {
            margin-top: 1rem !important;
            /* Kurangi dari mt-4 ke mt-1 */
        }

        /* Penyesuaian khusus untuk card tabel */
        .pull-up {
            margin-top: -1.5rem;
            /* Menarik tabel ke atas mendekati card statistik */
        }

        .card-header.pb-0 {
            padding-bottom: 0.5rem !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-content">
        <section class="row">
                        {{-- Banner Welcome --}}
            <div class="col-12">
                <div class="welcome-card mb-4 shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="fw-bold mb-2">Halo, {{ Auth::user()->name }}! 👋</h2>
                            <p class="mb-0">Semangat untuk hari ini! Periksa jadwal dan tugas Anda di bawah ini agar semua
                                berjalan lancar.</p>
                        </div>
                    </div>
                    <i class="bi bi-briefcase"></i>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                {{-- Statistik Ringkas --}}
                <div class="row">
                    {{-- TUGAS BULAN INI --}}
                    <div class="col-6 col-md-4 mb-4">
                        <div class="card shadow-sm border-0" style="border-top: 5px solid #435ebe !important;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon purple me-3"
                                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-calendar-check-fill text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted text-xs mb-1">BULAN INI</h6>
                                        <h4 class="fw-extrabold mb-0 text-primary">{{ $tugasBulanIni }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TUGAS SELESAI --}}
                    <div class="col-6 col-md-4 mb-4">
                        <div class="card shadow-sm border-0" style="border-top: 5px solid #198754 !important;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon green me-3"
                                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-check-circle-fill text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted text-xs mb-1">SELESAI</h6>
                                        <h4 class="fw-extrabold mb-0 text-success">{{ $tugasSelesai }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SISA TUGAS --}}
                    <div class="col-12 col-md-4 mb-4">
                        <div class="card shadow-sm border-0" style="border-top: 5px solid #ffc107 !important;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon orange me-3"
                                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #ffc107;">
                                        <i class="bi bi-clock-history text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted text-xs mb-1">SISA TUGAS</h6>
                                        <h4 class="fw-extrabold mb-0 text-warning">{{ $sisaTugas }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABEL MODEL MASTER DATA --}}
                <div class="card border-0 shadow-sm pull-up">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="fw-bold mb-0">Log Jadwal Tugas Saya</h5>
                    </div>
                    <div class="card-body mt-2">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle w-100" id="tableJadwalKru">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 15%">Tanggal</th>
                                        <th style="width: 15%">Nama</th>
                                        <th style="width: 25%">Alamat</th>
                                        <th style="width: 15%">Paket</th>
                                        <th style="width: 15%">Asisten</th>
                                        <th style="width: 10%">FG</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sisi Kanan: Widget --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-left: 5px solid #435ebe !important;">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3">Tugas Selanjutnya</h6>
                        @if($nextJob)
                                <div>
                                    <div class="fw-bold h6 text-dark">{{ $nextJob->nama }}</div>
                                            <div class="text-muted small mb-2">
                                    <i class="bi bi-calendar3 me-2"></i>
                                    {{ $nextJob->tanggal_awal }} {{ $nextJob->bulan }} {{ $nextJob->tahun }}
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-geo-alt me-2"></i> {{ $nextJob->alamat }}
                                </div>
                            </div>
                            <a href="{{ route('kru.jadwal.index') }}" class="btn btn-primary btn-sm w-100 mt-3 shadow-sm fw-bold">
                                <i class="bi bi-eye me-1"></i> Lihat Agenda Kerja
                            </a>
                        @else
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-x fs-2 text-muted"></i>
                                  <p class="text-muted small mt-2">Tidak ada tugas bulan ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="alert alert-light-secondary shadow-sm border-0">
                <h6 class="fw-bold"><i class="bi bi-megaphone me-2 text-primary"></i>Pengumuman</h6>
                <p class="text-xs mb-0 text-dark">Mohon hadir 1 jam sebelum acara dimulai. Gunakan seragam polo hitam
                    perusahaan.</p>
            </div>
    </div>
    </section>
    </div>
@endsection

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js">
        </script>

        <script>
            $(document).ready(function () {
                let table = $('#tableJadwalKru').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false, // Penting agar kolom tidak melar otomatis
                    ajax: {
                        url: "{{ route('kru.dashboard.data') }}",
                    },
                    pageLength: 5,
                    lengthMenu: [5, 10, 25, 50],
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                        { data: 'tanggal_custom', name: 'tanggal_awal' },
                        { data: 'nama', name: 'nama', class: 'fw-bold' },
                        { data: 'alamat', name: 'alamat' },
                        { data: 'nama_paket', name: 'nama_paket' },
                        { data: 'asisten', name: 'asisten' },
                        { data: 'fg', name: 'fg' }
                    ],
                    order: [[1, 'asc']],
                    language: {
                        search: "Cari:",
                        lengthMenu: "_MENU_",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ entri"
                    }
                });
            });
        </script>
@endpush