@extends('layouts.master')
@section('title', 'Monitor Jadwal Dekor')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}" class="text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary">Monitor Jadwal Dekorasi</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0">Laporan Jadwal Dekorasi</h3>
            <p class="text-muted small">Pantau logistik dan rincian dekorasi lapangan secara real-time.</p>
        </div>
        <div class="col-12 col-md-6 d-flex justify-content-md-end mt-3 mt-md-0">
            <button id="btn-print" class="btn btn-secondary shadow-sm px-3 fw-bold">
                <i class="bi bi-printer me-1"></i> Cetak PDF
            </button>
        </div>
    </div>
    <hr>
</div>

<section class="section">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pb-0 d-flex flex-wrap align-items-center gap-3">
            <h5 class="card-title mb-0 me-auto">Log Visual Dekorasi</h5>
            
            <div class="d-flex align-items-center gap-2">
                <input type="date" id="filter-tanggal" class="form-control form-control-sm shadow-sm" style="width: 140px;">
                
                <select id="filter-bulan" class="form-select form-select-sm shadow-sm" style="width: 130px;">
                    <option value="">Semua Bulan</option>
                    @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $i => $b)
                        <option value="{{ $b }}" {{ $i + 1 == date('n') ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>

                <select id="filter-tahun" class="form-select form-select-sm shadow-sm" style="width: 100px;">
                    <option value="">Semua</option>
                    @for ($y = date('Y'); $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
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
        // Inisialisasi DataTable
        let table = $('#jadwal-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('owner.jadwaldekor.data') }}",
                data: function (d) {
                    d.tanggal = $('#filter-tanggal').val();
                    d.bulan = $('#filter-bulan').val();
                    d.tahun = $('#filter-tahun').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                { data: 'tanggal_full', name: 'tanggal_awal' },
                { data: 'nama', class: 'fw-bold text-primary' },
                { data: 'paket_nama' },
                { data: 'foto', orderable: false, searchable: false, class: 'text-center' },
                { data: 'deskripsi' }
            ]
        });

        // Event Trigger: Filter otomatis saat ada perubahan
        $('#filter-tanggal, #filter-bulan, #filter-tahun').on('change', function () {
            table.draw();
        });

        // Event Trigger: Print
        $('#btn-print').on('click', function () {
            let params = new URLSearchParams({
                tanggal: $('#filter-tanggal').val(),
                bulan: $('#filter-bulan').val(),
                tahun: $('#filter-tahun').val()
            });
            window.open("{{ route('owner.jadwaldekor.print') }}?" + params.toString(), '_blank');
        });
    });
</script>
@endpush