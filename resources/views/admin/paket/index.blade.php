@extends('layouts.master')
@section('title', 'Data Paket Pernikahan')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-4">
                    <h3 class="fw-bold mb-0">Data Paket Pernikahan</h3>
                    <p class="text-muted mb-0">Kelola rincian layanan berdasarkan periode dan kategori.</p>
                </div>

                <div class="col-12 col-md-8 d-flex justify-content-md-end align-items-center mt-3 mt-md-0 gap-2">

                    {{-- Filter Kategori --}}
                    <select id="filter-kategori" class="form-select shadow-sm" style="width: 150px;">
                        <option value="">Semua Paket</option>
                        <option value="Normal">Paket Normal</option>
                        <option value="Promo">Paket Promo</option>
                        <option value="Expo">Paket Expo</option>
                    </select>
                    {{-- Filter Tahun --}}
                    <select id="filter-tahun" class="form-select shadow-sm" style="width: 130px;">
                        <option value="">Semua Tahun</option>
                        @for ($i = 2028; $i >= (date('Y') - 3); $i--)
                            <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>

                    <a href="{{ route('admin.paket.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Paket
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
                    <table class="table table-hover align-middle pakets-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" width="10%">No</th>
                                <th>Nama Paket</th>
                                <th>MakeUp</th>
                                <th>Dekorasi</th>
                                <th>Dokumentasi</th>
                                <th>Include</th>
                                <th>Free</th>
                                <th width="15%">Harga</th>
                                <th class="text-center" width="120px">Action</th>
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
            let table = $('.pakets-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.paket.data') }}",
                    data: function (d) {
                        d.tahun = $('#filter-tahun').val();
                        d.kategori = $('#filter-kategori').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'nama_paket', name: 'nama_paket', class: 'fw-bold' },
                    { data: 'makeup', name: 'makeup' },
                    { data: 'dekorasi', name: 'dekorasi' },
                    { data: 'dokumentasi', name: 'dokumentasi' },
                    { data: 'include', name: 'include' },
                    { data: 'bonus', name: 'bonus' },
                    {
                        data: 'harga',
                        name: 'harga',
                        render: function (data) {
                            return `<div class="text-primary fw-bold">Rp ${parseInt(data).toLocaleString('id-ID')}</div>`;
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ]
            });

            // Trigger redraw saat filter berubah
            $('#filter-tahun, #filter-kategori').on('change', function () {
                table.draw();
            });
        });

        function hapus(id) {
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Data Paket akan dihapus!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/paket/destroy') }}/" + id,
                        type: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (res) {
                            Swal.fire({ icon: "success", title: "Berhasil!", text: res.message, timer: 1500, showConfirmButton: false });
                            $('.pakets-table').DataTable().ajax.reload(null, false);
                        }
                    });
                }
            });
        }
    </script>

    @if(session('swal_success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('swal_success') }}",
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif
@endpush