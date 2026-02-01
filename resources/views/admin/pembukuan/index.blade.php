@extends('layouts.master')
@section('title', 'Kelola Pembukuan')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Pembukuan</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Pembukuan Harian</h3>
                <p class="text-muted mb-0 small">Monitoring arus kas masuk dan keluar secara real-time.</p>
            </div>
            <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center gap-2 mt-3 mt-md-0">
                <input type="date" id="tanggal" class="form-control shadow-sm w-auto" value="{{ $tanggal }}">
                <button type="button" id="btn-print" class="btn btn-primary shadow-sm px-3 fw-bold">
                    <i class="bi bi-printer me-1"></i> Cetak Laporan
                </button>
            </div>
        </div>
    </div>
    <hr>
</div>

<div class="page-content">
    {{-- Statistik Keuangan --}}
    <div class="row">
        <div class="col-6 col-lg-4">
            <div class="card shadow-sm border-0 border-start border-success border-4">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success me-3"><i class="bi bi-graph-up-arrow"></i></div>
                        <div>
                            <h6 class="text-muted font-semibold small">Total Pemasukan</h6>
                            <h5 class="font-extrabold mb-0 text-success" id="stat-pemasukan">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card shadow-sm border-0 border-start border-danger border-4">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-danger me-3"><i class="bi bi-graph-down-arrow"></i></div>
                        <div>
                            <h6 class="text-muted font-semibold small">Total Pengeluaran</h6>
                            <h5 class="font-extrabold mb-0 text-danger" id="stat-pengeluaran">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4 mt-3 mt-lg-0">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary me-3"><i class="bi bi-wallet2"></i></div>
                        <div>
                            <h6 class="text-muted font-semibold small">Saldo Bersih</h6>
                            <h5 class="font-extrabold mb-0 text-primary" id="stat-saldo">Rp {{ number_format($saldo, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        {{-- Tabel Pemasukan --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center pb-0 border-0">
                    <h5 class="mb-0 text-success fw-bold">Pemasukan</h5>
                    <a href="{{ route('admin.pembukuan.createPemasukan') }}" class="btn btn-success btn-sm">+ Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100" id="pemasukan-table">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th>Customer</th>
                                    <th>Nominal</th>
                                    <th class="text-center" width="100px">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Pengeluaran --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center pb-0 border-0">
                    <h5 class="mb-0 text-danger fw-bold">Pengeluaran</h5>
                    <a href="{{ route('admin.pembukuan.createPengeluaran') }}" class="btn btn-danger btn-sm">+ Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100" id="pengeluaran-table">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th>Keterangan</th>
                                    <th>Nominal</th>
                                    <th class="text-center" width="100px">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const getTanggal = urlParams.get('tanggal');

    if (getTanggal) { $('#tanggal').val(getTanggal); }

    const renderHarga = (data) => {
        let val = typeof data === 'string' ? data.replace(/[^0-9]/g, '') : data;
        let formatted = parseInt(val || 0).toLocaleString('id-ID');
        return `<span class="fw-bold">Rp ${formatted}</span>`;
    };

    let tablePemasukan = $('#pemasukan-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.pembukuan.pemasukanData') }}",
            data: d => { d.tanggal = $('#tanggal').val(); }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
            { data: 'customer', class: 'fw-bold' },
            { data: 'nominal', render: renderHarga },
            { data: 'action', orderable: false, searchable: false, class: 'text-center' }
        ]
    });

    let tablePengeluaran = $('#pengeluaran-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.pembukuan.pengeluaranData') }}",
            data: d => { d.tanggal = $('#tanggal').val(); }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
            { data: 'keterangan', class: 'fw-bold' },
            { data: 'nominal', render: renderHarga },
            { data: 'action', orderable: false, searchable: false, class: 'text-center' }
        ]
    });

    $('#tanggal').on('change', function() {
        const tgl = $(this).val();
        const newUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname}?tanggal=${tgl}`;
        window.history.pushState({ path: newUrl }, '', newUrl);

        tablePemasukan.draw();
        tablePengeluaran.draw();

        $.get("{{ route('admin.pembukuan.getSaldo') }}", { tanggal: tgl }, function(res) {
            $('#stat-pemasukan').text('Rp ' + parseInt(res.pemasukan).toLocaleString('id-ID'));
            $('#stat-pengeluaran').text('Rp ' + parseInt(res.pengeluaran).toLocaleString('id-ID'));
            $('#stat-saldo').text('Rp ' + parseInt(res.saldo).toLocaleString('id-ID'));
        });
    });

    $('#btn-print').on('click', function() {
        const tgl = $('#tanggal').val();
        if (tgl) {
            window.open("{{ route('admin.pembukuan.print') }}?tanggal=" + tgl, '_blank');
        } else {
            Swal.fire("Peringatan", "Silakan pilih tanggal terlebih dahulu", "warning");
        }
    });

    if (getTanggal) { $('#tanggal').trigger('change'); }
});

function hapusPembukuan(id) {
    Swal.fire({
        title: "Hapus Transaksi?",
        text: "Data keuangan yang dihapus tidak dapat dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Ya, Hapus!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ url('admin/pembukuan/destroy') }}/" + id,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    Swal.fire({ icon: res.success ? 'success' : 'error', title: res.success ? 'Berhasil!' : 'Gagal!', text: res.message, timer: 1500, showConfirmButton: false });
                    if (res.success) {
                        $('#pemasukan-table').DataTable().ajax.reload(null, false);
                        $('#pengeluaran-table').DataTable().ajax.reload(null, false);
                        $('#tanggal').trigger('change');
                    }
                }
            });
        }
    });
}
</script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 1500, showConfirmButton: false });
    });
</script>
@endif
@endpush