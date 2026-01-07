@extends('layouts.master')
@section('title', 'Manajemen Pembayaran GRA')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <style>
        .bg-light-warning { background-color: rgba(255, 193, 7, 0.15) !important; color: #ffc107 !important; }
        .bg-light-success { background-color: rgba(25, 135, 84, 0.15) !important; color: #198754 !important; }
        .bg-light-danger { background-color: rgba(220, 53, 69, 0.15) !important; color: #dc3545 !important; }
        .bg-light-primary { background-color: rgba(67, 94, 190, 0.15) !important; color: #435ebe !important; }
        .text-tagihan { color: #d63384; font-weight: bold; }
        .img-tf { cursor: pointer; transition: 0.3s; border: 1px solid #ddd; object-fit: cover; }
        .img-tf:hover { transform: scale(1.05); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-7">
                    <h3 class="fw-bold mb-0">Kelola Pembayaran & Cicilan</h3>
                    <p class="text-muted small">Pantau DP 2jt, Cicilan Fitting, dan Pelunasan Wedding.</p>
                </div>
                <div class="col-12 col-md-5 d-flex justify-content-md-end gap-2">
                    <button class="btn btn-outline-secondary shadow-sm fw-bold" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card bg-light-primary border-0 p-3 shadow-sm">
                    <small class="fw-bold text-uppercase opacity-75">Syarat Fiks Booking</small>
                    <h5 class="mb-0 fw-bold">Wajib DP Rp 2.000.000</h5>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-pembayaran" style="width:100%">
                        <thead>
                            <tr class="text-nowrap">
                                <th class="text-center">No</th>
                                <th>Nama Pengantin</th>
                                <th>Harga Paket</th>
                                <th class="text-center">Bukti TF</th>
                                <th>Jumlah Bayar</th>
                                <th>Total Masuk</th>
                                <th>Sisa Tagihan</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Preview --}}
    <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent text-center">
                <div class="modal-body p-0">
                    <img src="" id="fotoFull" class="img-fluid rounded shadow-lg border border-white border-4">
                    <div class="mt-3">
                        <button type="button" class="btn btn-dark fw-bold shadow-sm" data-bs-dismiss="modal">Tutup Preview</button>
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
        $(document).ready(function () {
            $('#table-pembayaran').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.pembayaran.data') }}", 
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'pengantin', name: 'pengantin' }, 
                    { data: 'harga', name: 'harga' },
                    { data: 'bukti_transfer', name: 'bukti_transfer', orderable: false, class: 'text-center' },
                    { data: 'jumlah_bayar', name: 'jumlah_bayar', class: 'fw-bold' },
                    { data: 'total_masuk', name: 'total_masuk' },
                    { data: 'sisa_tagihan', name: 'sisa_tagihan', class: 'text-tagihan' },
                    { data: 'status_pembayaran', name: 'status_pembayaran', class: 'text-center' },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false, class: 'text-center' }
                ],
                order: [[4, 'desc']], 
                language: {
                    search: "Cari Pengantin:",
                    lengthMenu: "_MENU_ entri per halaman",
                }
            });
        });

        function viewFoto(url) {
            $('#fotoFull').attr('src', url);
            $('#modalFoto').modal('show');
        }

        function verifikasi(id, status) {
            let teks = status === 'valid' ? 'Validasi Pembayaran?' : 'Tolak Pembayaran ini?';
            Swal.fire({
                title: teks,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: status === 'valid' ? '#198754' : '#d33',
                confirmButtonText: 'Ya, Proses!'
            }).then((res) => {
                if (res.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/pembayaran') }}/" + id + "/status",
                        type: "PUT",
                        data: { _token: "{{ csrf_token() }}", status: status },
                        success: function (res) {
                            $('#table-pembayaran').DataTable().ajax.reload();
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
                        }
                    });
                }
            });
        }
    </script>
@endpush