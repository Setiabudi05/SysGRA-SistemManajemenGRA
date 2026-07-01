@extends('layouts.master')
@section('title', 'Data Item Tambahan')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-7">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Add-ons</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Data Item Tambahan</h3>
                </div>
                <div class="col-12 col-md-5 d-flex justify-content-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.addons.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Add-on
                    </a>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle addons-table" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama Item</th>
                            <th>Deskripsi</th>
                            <th width="20%">Harga</th>
                            <th class="text-center" width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
            $('.addons-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.addons.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_item', name: 'nama_item' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'harga', name: 'harga', render: (data) => 'Rp ' + parseInt(data).toLocaleString('id-ID') },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 1500,
                showConfirmButton: false
            });
        @endif
        function hapusAddon(id) {
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Data item tambahan ini akan dihapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/addons/destroy') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (res) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "Data berhasil dihapus.",
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // Refresh tabel secara otomatis
                            $('.addons-table').DataTable().ajax.reload(null, false);
                        },
                        error: function () {
                            Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus data.", "error");
                        }
                    });
                }
            });
        }


    </script>
@endpush