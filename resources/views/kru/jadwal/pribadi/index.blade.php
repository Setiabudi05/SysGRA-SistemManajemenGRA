@extends('layouts.master')
@section('title', 'Jadwal Pribadi & Availability')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <style>
        #pribadi-table th,
        #pribadi-table td {
            font-size: 0.85rem;
        }

        .card-header {
            padding: 1.5rem 1.5rem 0 1.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <h3 class="fw-bold text-dark mb-0">Manajemen Jadwal Pribadi</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mt-2 mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('kru.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Jadwal Pribadi</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('kru.jadwal.pribadi.create') }}" class="btn btn-primary shadow-sm fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Agenda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div
                class="card-header bg-transparent d-flex flex-wrap align-items-center justify-content-between gap-3 border-0 pt-4">
                <h5 class="fw-bold mb-0">Log Kesibukan & Vendor Lain</h5>

                {{-- Filter Container --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <input type="date" id="filter-tanggal" class="form-control form-control-sm" style="width: 140px;">

                    <select id="filter-bulan" class="form-select form-select-sm" style="width: 130px;">
                        <option value="">Semua Bulan</option>
                        @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>

                    <select id="filter-tahun" class="form-select form-select-sm" style="width: 100px;">
                        <option value="">Tahun</option>
                        @for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="pribadi-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Event/Acara</th>
                                <th>Vendor</th>
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

    <script>
        $(document).ready(function () {
            var table = $('#pribadi-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{!! route('kru.jadwal.pribadi.data') !!}",
                    data: function (d) {
                        d.tanggal = $('#filter-tanggal').val();
                        d.bulan = $('#filter-bulan').val();
                        d.tahun = $('#filter-tahun').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'event_date', name: 'event_date', defaultContent: '-' },
                    { data: 'tipe', name: 'tipe', render: data => data == 'INTERNAL' ? '<span class="badge bg-primary">INTERNAL</span>' : '<span class="badge bg-warning text-dark">EKSTERNAL</span>' },
                    { data: 'nama_event', name: 'nama_event', defaultContent: '-' },
                    { data: 'nama_vendor', name: 'nama_vendor', defaultContent: '-' },
                    { data: 'keterangan', name: 'keterangan', defaultContent: '-' },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
                ]
            });

            // Event filter
            $('#filter-tanggal, #filter-bulan, #filter-tahun').on('change', function () {
                if ($(this).attr('id') === 'filter-tanggal' && $(this).val() !== '') {
                    $('#filter-bulan, #filter-tahun').val('');
                }
                table.ajax.reload();
            });
        });
    </script>
@endpush