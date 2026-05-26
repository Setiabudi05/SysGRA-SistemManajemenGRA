@extends('layouts.master')
@section('title', 'Data Jadwal Layos')

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
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Jadwal Layos</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Data Jadwal Layos</h3>
                <p class="text-muted mb-0 small">Monitor ketersediaan tenda dan perlengkapan pesta.</p>
            </div>
            <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center mt-3 mt-md-0 gap-2">
                <button id="btn-print" class="btn btn-secondary shadow-sm px-3">
                    <i class="bi bi-printer"></i> Cetak Jadwal
                </button>
                <a href="{{ route('admin.jadwallayos.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                    <i class="bi bi-plus-lg"></i> Tambah Jadwal
                </a>
            </div>
        </div>
    </div>
    <hr>
</div>

<section class="section">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pb-0">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <h5 class="card-title mb-0 me-auto">Log Operasional Layos</h5>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted">Bulan:</span>
                    <select id="filter-bulan" class="form-select form-select-sm shadow-sm">
                        <option value="">Semua</option>
                        @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted">Tahun:</span>
                    <select id="filter-tahun" class="form-select form-select-sm shadow-sm">
                        @for ($y = date('Y') + 2; $y >= date('Y') - 1; $y--)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="jadwal-layos-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Paket</th>
                            <th>Layos</th>
                            <th class="text-center" width="120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@include('sweetalert::alert')
@endsection

@push('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const getBulan = urlParams.get('bulan');
    const getTahun = urlParams.get('tahun');
    const monthNames = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
    let now = new Date();
    
    if (getBulan) {
        $('#filter-bulan').val(getBulan);
    } else if (!$('#filter-bulan').val()) {
        $('#filter-bulan').val(monthNames[now.getMonth()]);
    }

    if (getTahun) {
        $('#filter-tahun').val(getTahun);
    } else if (!$('#filter-tahun').val()) {
        $('#filter-tahun').val(now.getFullYear());
    }

    let table = $('#jadwal-layos-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.jadwallayos.data') }}", 
            data: function(d) {
                d.bulan = $('#filter-bulan').val();
                d.tahun = $('#filter-tahun').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false, class: 'text-center' },
            { data: 'tanggal_full', name: 'tanggal_awal' },
            { data: 'nama', name: 'nama', class: 'fw-bold text-primary' },
            { data: 'alamat', name: 'alamat' },
            { data: 'paket', name: 'paket' },
            { data: 'layos_detail', name: 'layos_detail' },
            { data: 'action', name: 'action', orderable:false, searchable:false, class: 'text-center' }
        ]
    });

    $('#filter-bulan, #filter-tahun').change(() => table.draw());

    $('#btn-print').click(function () {
        let url = "{{ route('admin.jadwallayos.print') }}?bulan=" + $('#filter-bulan').val() + "&tahun=" + $('#filter-tahun').val();
        window.open(url, '_blank');
    });
});

function hapusJadwal(id){
    if (id === null || id === 'null') {
        Swal.fire("Info", "Rincian belum diisi, tidak ada data untuk dihapus.", "info");
        return;
    }

    Swal.fire({
        title: "Apakah Anda Yakin?",
        text: "Data rincian ini akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
    }).then((result)=>{
        if(result.isConfirmed){
            $.ajax({
                url: "{{ url('admin/jadwallayos/destroy') }}/" + id, 
                type: "DELETE",
                data: {_token: "{{ csrf_token() }}"},
                success:function(res){
                    Swal.fire({
                        icon: res.success ? 'success' : 'error',
                        title: res.success ? 'Berhasil!' : 'Gagal!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    if(res.success){
                        $('#jadwal-layos-table').DataTable().ajax.reload(null, false);
                    }
                }
            });
        }
    });
}
</script>
@endpush