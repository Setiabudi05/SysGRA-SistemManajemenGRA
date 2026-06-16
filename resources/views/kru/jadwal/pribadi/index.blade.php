@extends('layouts.master')
@section('title', 'Jadwal Pribadi & Availability')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <style>
        #pribadi-table th, #pribadi-table td { font-size: 0.85rem; }
        .card-header { padding: 1.5rem 1.5rem 0 1.5rem; }
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
            <div class="card-header bg-transparent border-0">
                <h5 class="fw-bold mb-0">Log Kesibukan & Vendor Lain</h5>
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
            $('#pribadi-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{!! route('kru.jadwal.pribadi.data') !!}", // Pastikan route ini ada
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'event_date', name: 'event_date' },
                    { data: 'tipe', name: 'tipe', render: function(data) {
                        return data == 'INTERNAL' ? '<span class="badge bg-primary">INTERNAL</span>' : '<span class="badge bg-warning text-dark">EKSTERNAL</span>';
                    }},
                    { data: 'nama_event', name: 'nama_event' },
                    { data: 'nama_vendor', name: 'nama_vendor', defaultContent: '-' },
                    { data: 'keterangan', name: 'keterangan' },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
                ]
            });
        });
    </script>
@endpush