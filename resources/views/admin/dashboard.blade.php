@extends('layouts.master')
@section('title', 'Dashboard Strategis')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        .stats-icon i {
            font-size: 1.4rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .badge-ai {
            background: linear-gradient(45deg, #435ebe, #55c6e8);
            color: white;
            font-size: 0.65rem;
            padding: 0.2rem 0.5rem;
            border-radius: 50px;
        }

        .x-small {
            font-size: 0.75rem;
        }

        /* Memastikan tabel tetap rapi dan tidak meluber */
        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }

        #tableJadwal {
            width: 100% !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                {{-- Breadcrumb agar terlihat seperti sistem profesional --}}
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Home</a>
                        </li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Dashboard Strategis</h3>
                <p class="text-muted small">
                    &nbsp; Selamat datang kembali, {{ auth()->user()->name ?? 'Administrator' }}
                </p>
            </div>
        </div>


        <div class="page-content">
            {{-- STATISTIK CARD --}}
            <div class="row">
                @foreach([['PAKET', $totalPaket, 'bi-stack', 'purple'], ['DEKORASI', $totalDekorasi, 'bi-flower1', 'blue'], ['PELANGGAN', $totalPelanggan, 'bi-people-fill', 'green'], ['BOOKING', $totalBooking, 'bi-calendar-check', 'red']] as $s)
                    <div class="col-6 col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-body px-3 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon {{ $s[3] }} me-3"><i class="bi {{ $s[2] }}"></i></div>
                                    <div>
                                        <h6 class="text-muted x-small font-semibold">{{ $s[0] }}</h6>
                                        <h4 class="font-extrabold mb-0">{{ $s[1] }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- JADWAL OPERASIONAL (DATA TABLES) --}}
            <section class="row mt-3">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Jadwal Operasional: {{ \Carbon\Carbon::now()->format('F Y') }}</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover align-middle" id="tableJadwal">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th>Paket</th>
                                        <th>Asisten</th>
                                        <th>FG</th>
                                        <th>Layos</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingSchedules as $index => $js)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-bold">{{ \Carbon\Carbon::parse($js->tanggal_awal)->format('d M Y') }}
                                            </td>
                                            <td>{{ $js->nama }}</td>
                                            <td>{{ $js->alamat }}</td>
                                            <td>
                                                @php $paketData = json_decode($js->paket, true); @endphp
                                                {{ is_array($paketData) ? ($paketData['nama_paket'] ?? 'N/A') : $js->paket }}
                                            </td>
                                            <td>{{ $js->asisten ?? '-' }}</td>
                                            <td>{{ $js->fg ?? '-' }}</td>
                                            <td>{{ $js->layos ?? '-' }}</td>
                                            <td>{{ $js->keterangan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
@endsection

    @push('js')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
        <script>
            $(document).ready(function () {
                // Menggunakan scrollX agar tabel yang banyak kolom tidak merusak layout
                $('#tableJadwal').DataTable({
                    scrollX: true,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampil _MENU_ baris"
                    }
                });
            });
        </script>
    @endpush