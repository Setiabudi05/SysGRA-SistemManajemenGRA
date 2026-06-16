@extends('layouts.master')
@section('title', 'Laporan Pembukuan')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <style>
        .stats-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }

        .stats-icon.green {
            background-color: #435ebe;
        }

        .stats-icon.red {
            background-color: #ff7976;
        }

        .stats-icon.blue {
            background-color: #57caeb;
        }

        .table.small {
            font-size: 0.85rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h3 class="fw-bold">Laporan Pembukuan</h3>
                    <p class="text-muted mb-0">Monitoring arus kas masuk dan keluar secara real-time.</p>
                </div>
                <div class="col-md-5 d-flex justify-content-md-end align-items-center gap-2 mt-3 mt-md-0">
                    <form action="{{ route('owner.pembukuan.index') }}" method="GET" class="d-flex gap-2">
                        {{-- Cukup satu input date saja --}}
                        <input type="date" name="tanggal" class="form-control form-control-sm shadow-sm"
                            value="{{ $tanggalDipilih }}">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-filter"></i></button>
                    </form>
                    <button onclick="printReport()" class="btn btn-secondary btn-sm fw-bold shadow-sm">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </button>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        {{-- Card Ringkasan --}}
        <div class="row">
            <div class="col-md-4">
                <div class="card border-start border-success border-4 shadow-sm">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 d-flex justify-content-start">
                                <div class="stats-icon green mb-2"><i class="bi bi-graph-up"></i></div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold small uppercase">Total Pemasukan</h6>
                                <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($pemasukan, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-danger border-4 shadow-sm">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 d-flex justify-content-start">
                                <div class="stats-icon red mb-2"><i class="bi bi-graph-down"></i></div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold small uppercase">Total Pengeluaran</h6>
                                <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-primary border-4 shadow-sm">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 d-flex justify-content-start">
                                <div class="stats-icon blue mb-2"><i class="bi bi-wallet2"></i></div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold small uppercase">Saldo Bersih</h6>
                                <h5 class="fw-bold mb-0 text-primary">Rp {{ number_format($saldo, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Rincian --}}
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h6 class="fw-bold text-success"><i class="bi bi-arrow-down-left-circle me-2"></i>Pemasukan</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover small w-100" id="table-pemasukan">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Customer</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listPemasukan as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->customer ?? '-' }}</td>
                                            <td class="fw-bold text-success">Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h6 class="fw-bold text-danger"><i class="bi bi-arrow-up-right-circle me-2"></i>Pengeluaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover small w-100" id="table-pengeluaran">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Keterangan</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listPengeluaran as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->keterangan ?? '-' }}</td>
                                            <td class="fw-bold text-danger">Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
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
            $('#table-pemasukan, #table-pengeluaran').DataTable({
                "pageLength": 5,
                "lengthMenu": [5, 10, 25],
                "language": { "emptyTable": "Tidak ada transaksi" }
            });
        });

        function printReport() {
            const start = document.querySelector('input[name="start_date"]').value;
            const end = document.querySelector('input[name="end_date"]').value;
            window.open(`{{ route('owner.pembukuan.print') }}?start_date=${start}&end_date=${end}`, '_blank');
        }
    </script>
@endpush