@extends('layouts.master')
@section('title', 'Laporan Pesanan')

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
                            <li class="breadcrumb-item active text-primary" aria-current="page">Laporan Pesanan</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Laporan Pesanan Pengantin</h3>
                    <p class="text-muted mb-0">Monitoring data pesanan berdasarkan jadwal operasional.</p>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 pt-3 bg-transparent">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <select id="filter-status" class="form-select form-select-sm shadow-sm" style="width: 140px;">
                        <option value="">Status...</option>
                        <option value="PENDING">Pending</option>
                        <option value="CONFIRMED">Confirmed</option>
                        <option value="COMPLETED">Completed</option>
                        <option value="DRAFT">Draft</option>
                    </select>

                    <input type="date" id="tgl-acara" class="form-control form-control-sm shadow-sm" style="width: 140px;"
                        title="Pilih Tanggal">

                    <button id="btn-reset" class="btn btn-sm btn-secondary shadow-sm" title="Reset Filter">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="jadwal-table">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Tanggal</th> {{-- Kolom Gabungan --}}
                                <th>Nama Pengantin</th>
                                <th>Paket</th>
                                <th>Harga</th>
                                <th class="text-center">Status</th>
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
            let table = $('#jadwal-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('owner.booking.data') }}",
                    data: function (d) {
                        d.status = $('#filter-status').val();
                        d.tgl_acara = $('#tgl-acara').val();
                    }
                },
                // Di dalam script DataTables
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'tanggal_full', name: 'event_date' },
                    { data: 'nama', name: 'bride_groom_name' }, // Harus 'nama' sesuai Controller
                    { data: 'paket.nama_paket', name: 'paket.nama_paket', defaultContent: '-' },
                    {
                        data: 'harga_paket',
                        name: 'paket.harga',
                        render: function (data) {
                            return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID');
                        }
                    },
                    { data: 'status', name: 'status', class: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ]
            });

            $('#filter-status, #tgl-acara').on('change', function () {
                table.draw();
            });

            // Event Trigger: Reset Filter
            $('#btn-reset').on('click', function () {
                $('#filter-status').val('');
                $('#tgl-acara').val('');
                table.draw();
            });
        });
    </script>
@endpush