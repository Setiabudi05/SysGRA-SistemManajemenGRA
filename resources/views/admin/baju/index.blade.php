@extends('layouts.master')
@section('title', 'Koleksi Baju Pengantin')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <style>
        /* Agar isi tabel sejajar tengah vertikal */
        #baju-table tbody tr td { vertical-align: middle !important; }

        /* Container foto dibuat lebih tinggi agar portrait tampak besar & utuh */
        .img-container { 
            width: 130px; 
            height: 170px; 
            overflow: hidden; 
            border-radius: 8px; 
            background-color: #f8f9fa; 
            border: 1px solid #e2e8f0; 
            margin: 0 auto;
            display: flex; 
            align-items: center; 
            justify-content: center;
        }

        /* Menggunakan contain agar seluruh foto tampil tanpa terpotong */
        .img-container img { 
            max-width: 100%; 
            max-height: 100%; 
            object-fit: contain; 
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="row align-items-center">
            <div class="col-12 col-md-7">
                <h3 class="fw-bold mb-0">Inventory Baju Pengantin</h3>
                <p class="text-muted mb-0">Kelola koleksi baju pengantin secara terpusat.</p>
            </div>
            <div class="col-12 col-md-5 text-md-end mt-3 d-flex justify-content-md-end gap-2">
                <a href="{{ route('admin.baju.print') }}" target="_blank" class="btn btn-secondary shadow-sm px-3 fw-bold">
                    <i class="bi bi-printer me-1"></i> Cetak Katalog
                </a>
                <a href="{{ route('admin.baju.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                    <i class="bi bi-plus-lg"></i> Tambah Koleksi
                </a>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle" id="baju-table" style="width:100%">
                    <thead>
                        <tr style="vertical-align: middle;">
                            <th class="text-center" style="width: 5%">No</th>
                            <th class="text-center" style="width: 20%">Foto</th>
                            <th style="width: 25%">Gown</th>
                            <th style="width: 20%">Paket</th>
                            <th style="width: 15%">Deskripsi</th>
                            <th class="text-center" style="width: 15%">Aksi</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Inisialisasi DataTable
            $('#baju-table').DataTable({
                processing: true, 
                serverSide: true,
                ajax: "{{ route('admin.baju.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center' },
                    { data: 'foto', name: 'foto', orderable: false, searchable: false },
                    { data: 'nama_gown', name: 'nama_gown' },
                    { data: 'paket', name: 'paket' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Alert sukses (Tambah/Update)
            @if(session('success'))
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Berhasil!', 
                    text: "{{ session('success') }}", 
                    timer: 1500, 
                    showConfirmButton: false 
                });
            @endif

            // Event Hapus dengan Event Delegation
            $('body').on('click', '.hapus-btn', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: "Yakin Hapus?", 
                    text: "Data tidak dapat dikembalikan!", 
                    icon: "warning",
                    showCancelButton: true, 
                    confirmButtonColor: "#d33", 
                    confirmButtonText: "Ya, Hapus!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/baju/destroy') }}/" + id,
                            type: "DELETE",
                            data: { _token: "{{ csrf_token() }}" },
                            success: function (res) {
                                Swal.fire({ 
                                    icon: 'success', 
                                    title: 'Berhasil!', 
                                    text: res.message, 
                                    timer: 1500, 
                                    showConfirmButton: false 
                                });
                                $('#baju-table').DataTable().ajax.reload(null, false);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush