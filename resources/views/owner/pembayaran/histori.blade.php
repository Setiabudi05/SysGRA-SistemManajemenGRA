@extends('layouts.master')
@section('title', 'Histori Pembayaran - ' . $jadwal->nama)

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.pembayaran.index') }}" class="text-muted">Laporan Pembayaran</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Histori</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Histori Pembayaran</h3>
                <p class="text-muted mb-0 small">Rincian cicilan untuk pengantin: <strong>{{ $jadwal->nama }}</strong></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first d-flex justify-content-md-end mb-3">
                <a href="{{ route('owner.pembayaran.index') }}" class="btn btn-secondary shadow-sm px-3 fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>
    <hr>
</div>

<section class="section">
    <div class="row">
        {{-- Sisi Kiri: Widget Ringkasan --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar avatar-xl bg-light-primary p-2 me-3">
                            <i class="bi bi-wallet2 text-primary fs-2"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small fw-bold text-uppercase">Total Tagihan</h6>
                            <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalHarga, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Sudah Dibayar</span>
                            <span class="text-success fw-bold">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted small fw-bold text-uppercase">Sisa Tagihan</span>
                            <span class="text-danger fw-bold">Rp {{ number_format(max(0, $sisaTagihan), 0, ',', '.') }}</span>
                        </div>
                        
                        @if($sisaTagihan <= 0 && $totalHarga > 0)
                            <div class="alert alert-success text-center fw-bold py-2 shadow-sm mb-0">
                                <i class="bi bi-check-circle-fill me-2"></i> LUNAS
                            </div>
                        @else
                            <div class="alert alert-light-danger text-danger text-center fw-bold py-2 border-dashed mb-0">
                                <i class="bi bi-exclamation-circle-fill me-2"></i> BELUM LUNAS
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Tabel Histori --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pb-0">
                    <h5 class="card-title mb-0">Log Transaksi Cicilan</h5>
                    {{-- Wadah Custom Search Box --}}
                    <div id="search-container"></div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100" id="table-histori">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th>Tanggal Bayar</th>
                                    <th>Metode</th>
                                    <th class="text-end">Nominal</th>
                                    <th class="text-center" width="10%">Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwal->pembayarans as $index => $bayar)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->translatedFormat('d F Y') }}</td>
                                    <td>
                                        <span class="badge bg-light-secondary text-secondary">
                                            {{ strtoupper($bayar->metode ?? 'Transfer') }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">Rp {{ number_format($bayar->nominal, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('owner.pembayaran.nota', $bayar->id) }}" class="btn btn-sm btn-outline-info shadow-sm" target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
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
    $(document).ready(function() {
        let table = $('#table-histori').DataTable({
            "pagingType": "full_numbers",
            "language": {
                "search": "Cari Transaksi:",
                "searchPlaceholder": "...",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "paginate": {
                    "first": "«",
                    "last": "»",
                    "next": "›",
                    "previous": "‹"
                }
            },
            // Custom DOM: t(table), i(info), p(pagination)
            // Search (f) sengaja dibuang dari DOM karena kita pindahkan manual ke header
            "dom": "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "pageLength": 5,
            "ordering": true,
            "order": [[1, "desc"]],
            "initComplete": function() {
                // Buat Search Box secara manual agar bisa ditaruh di header card
                let api = this.api();
                let filterHtml = $(`
                    <div class="dataTables_filter">
                        <label>Cari Transaksi: 
                            <input type="search" class="form-control form-control-sm" placeholder="...">
                        </label>
                    </div>
                `);

                filterHtml.find('input').on('keyup', function() {
                    api.search(this.value).draw();
                });

                $('#search-container').append(filterHtml);
            }
        });
    });
</script>
@endpush