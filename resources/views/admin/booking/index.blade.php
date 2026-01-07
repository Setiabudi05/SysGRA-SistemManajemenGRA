@extends('layouts.master')
@section('title', 'Manajemen Pesanan')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <style>
        .bg-light-warning { background-color: rgba(255, 193, 7, 0.15) !important; color: #ffc107 !important; }
        .bg-light-primary { background-color: rgba(67, 94, 190, 0.15) !important; color: #435ebe !important; }
        .bg-light-success { background-color: rgba(25, 135, 84, 0.15) !important; color: #198754 !important; }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-7">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active text-primary">Manajemen Pesanan</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Daftar Pesanan Masuk</h3>
                </div>
                <div class="col-12 col-md-5 d-flex justify-content-md-end gap-2 mt-3 mt-md-0">
                    {{-- Link ke halaman create jika ingin form besar, atau tetap modal --}}
                    <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-lg"></i> Tambah Pesanan
                    </button>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-booking" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Pemesan</th>
                                <th>WhatsApp</th>
                                <th>Pengantin</th>
                                <th>Tgl Acara</th>
                                <th>Paket</th>
                                <th>Harga</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formTambah">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Tambah Pesanan Offline</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">NAMA PEMESAN</label>
                                <input type="text" name="nama_pemesan" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">WHATSAPP</label>
                                <input type="number" name="whatsapp" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">NAMA PENGANTIN</label>
                                <input type="text" name="nama_pengantin" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">TANGGAL ACARA</label>
                                <input type="date" name="tanggal_acara" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">PAKET</label>
                                <input type="text" name="paket_pilihan" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">HARGA</label>
                                <input type="text" name="harga_paket" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">DURASI</label>
                                <input type="text" name="durasi_acara" class="form-control" value="1 Hari" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">ALAMAT LENGKAP</label>
                                <textarea name="alamat_acara" class="form-control" rows="2" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Pesanan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            let table = $('#table-booking').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.booking.data') }}",
                columns: [
                    // orderable: false pada DT_RowIndex untuk mencegah error sorting SQL
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'whatsapp_number', name: 'whatsapp_number' },
                    { data: 'bride_groom_name', name: 'bride_groom_name', class: 'fw-bold' },
                    { data: 'event_date', name: 'event_date' },
                    { data: 'package_name', name: 'package_name' },
                    { data: 'package_price', name: 'package_price' },
                    { data: 'status', name: 'status', class: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
                ],
                // Set default order ke kolom ke-4 (Tanggal Acara) agar tidak crash dengan kolom No
                order: [[4, 'desc']] 
            });

            $('#formTambah').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('admin.booking.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#modalTambah').modal('hide');
                        $('#formTambah')[0].reset();
                        table.ajax.reload();
                    },
                    error: function(err) {
                        Swal.fire('Gagal!', 'Pastikan semua data terisi dengan benar.', 'error');
                    }
                });
            });
        });

        function hapusBooking(id) {
            Swal.fire({
                title: "Hapus Pesanan?",
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/booking') }}/" + id,
                        type: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (res) {
                            Swal.fire('Terhapus!', res.message, 'success');
                            $('#table-booking').DataTable().ajax.reload();
                        }
                    });
                }
            });
        }
    </script>
@endpush