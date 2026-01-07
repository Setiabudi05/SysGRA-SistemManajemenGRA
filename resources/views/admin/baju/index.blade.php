@extends('layouts.master')
@section('title', 'Koleksi Baju Pengantin')

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
                            <li class="breadcrumb-item active text-primary" aria-current="page">Baju Pengantin</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Inventory Baju Pengantin</h3>
                    <p class="text-muted mb-0">Kelola koleksi dan stok inventory baju pengantin secara terpusat.</p>
                </div>

                <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center gap-2 mt-3 mt-md-0">
                    <a href="{{ route('admin.baju.print') }}" target="_blank" class="btn btn-secondary shadow-sm">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </a>
                    <a href="{{ route('admin.baju.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                        <i class="bi bi-plus-lg"></i> Tambah Koleksi
                    </a>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    {{-- PERBAIKAN: Gunakan ID yang unik dan dipanggil di JS --}}
                    <table class="table table-hover align-middle" id="baju-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Foto</th>
                                <th>Informasi Baju</th> {{-- Kolom Gabungan dari Controller --}}
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    // PERBAIKAN: Panggil ID yang sama dengan di HTML (#baju-table)
    let table = $('#baju-table').DataTable({ 
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.baju.data') }}",
        },
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        pageLength: 10,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'foto', name: 'foto', orderable: false, searchable: false, className: 'text-center' },
            { data: 'info_baju', name: 'info_baju' }, // Memanggil gabungan nama/kategori & warna dari Controller
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ]
    });
});

function hapusBaju(id) {
    Swal.fire({
        title: "Apakah Anda Yakin?",
        text: "Koleksi baju akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ url('admin/baju/destroy') }}/" + id,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function (res) {
                    Swal.fire({
                        icon: res.success ? 'success' : 'error',
                        title: res.success ? 'Berhasil!' : 'Gagal!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    if (res.success) {
                        // PERBAIKAN: Reload tabel yang benar
                        $('#baju-table').DataTable().ajax.reload(null, false);
                    }
                }
            });
        }
    });
}
</script>

@if(session('swal_success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('swal_success') }}",
            timer: 1500,
            showConfirmButton: false
        });
    });
</script>
@endif
@endpush