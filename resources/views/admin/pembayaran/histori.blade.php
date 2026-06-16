@extends('layouts.master')
@section('title', 'Histori Cicilan - Griya Rias Asmara')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">

@endpush

@section('content')
    {{-- Header & Breadcrumb: Mengikuti Gaya "Tambah Dekorasi" --}}
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                {{-- Sisi Kiri: Judul dan Navigasi --}}
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.pembayaran.index') }}"
                                    class="text-muted">Pembayaran</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Histori Cicilan</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Histori Cicilan</h3>
                    <p class="text-muted mb-0 small">Rincian transaksi untuk
                        <strong>{{ $booking->bride_groom_name }}</strong>.</p>
                </div>

                {{-- POSISI KANAN: Navigasi Teks Halus (Lurus dengan ujung kartu) --}}
                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.pembayaran.index') }}" class="text-muted small fw-bold text-decoration-none">
                        <i class="bi bi-chevron-left"></i> Kembali ke daftar pembayaran
                    </a>
                </div>
            </div>
        </div>
        <hr class="mb-4">
    </div>

    <section class="section">
        <div class="row g-4">
            {{-- Ringkasan Keuangan (Kiri) --}}
            <div class="col-md-4">
                <div class="card card-custom h-90 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <span class="summary-header">Ringkasan Keuangan</span>
                        {{-- Ringkasan Keuangan (Kiri) --}}
                        <div class="summary-row">
                            <span class="text-muted small">Total Tagihan</span>
                            <div class="summary-value text-dark">
                                <span>Rp</span>
                                <span>{{ number_format($booking->package_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="summary-row text-success">
                            <span class="small">Sudah Dibayar</span>
                            <div class="summary-value">
                                <span>Rp</span>
                                {{-- LOGIKA SAPU JAGAT: Hitung semua uang yang statusnya BUKAN pending --}}
                                <span>{{ number_format($booking->pembayarans->where('status_pembayaran', '!=', 'pending')->sum('jumlah_bayar'), 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="summary-row text-danger total-row">
                            <span class="fw-bold" style="font-size: 0.9rem;">Sisa Tagihan</span>
                            <div class="summary-value" style="width: 130px;">
                                <h6 class="fw-bold mb-0 d-flex justify-content-between w-100 text-danger">
                                    <span>Rp</span>
                                    @php
                                        // Hitung sisa tagihan real secara otomatis di view
                                        $totalTerbayarReal = $booking->pembayarans->where('status_pembayaran', '!=', 'pending')->sum('jumlah_bayar');
                                        $sisaTagihanReal = $booking->package_price - $totalTerbayarReal;
                                    @endphp
                                    <span>{{ number_format($sisaTagihanReal < 0 ? 0 : $sisaTagihanReal, 0, ',', '.') }}</span>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Daftar Transaksi (Kanan) --}}
            <div class="col-md-8">
                <div class="card card-custom border-0 shadow-sm">
                    <div class="card-body">
                        <table class="table table-hover align-middle histori-table w-100" id="table-histori">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th width="150px">Nominal</th>
                                    <th class="text-center" width="100px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->pembayarans as $p)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        {{-- Ganti baris lama Anda dengan ini --}}
                                        {{-- Ganti baris tanggal lama dengan ini --}}
                                        <td class="small">
                                            {{ \Carbon\Carbon::parse($p->created_at)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-light-primary text-primary fw-bold">
                                                {{ $p->keterangan ?? 'BIAYA AWAL' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="price-align text-primary">
                                                <span>Rp</span>
                                                <span>{{ number_format($p->jumlah_bayar, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group gap-1">
                                                <a href="{{ route('admin.pembayaran.nota', $p->id) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary shadow-sm" title="Cetak Nota">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                                <button type="button" onclick="hapusPembayaran('{{ $p->id }}')"
                                                    class="btn btn-sm btn-outline-danger shadow-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
            $('#table-histori').DataTable({
                language: {
                    search: "Search:",
                    lengthMenu: "_MENU_ &nbsp; entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        next: '<i class="bi bi-chevron-right"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>'
                    },
                    emptyTable: "Belum ada riwayat pembayaran."
                }
            });
        });

        function hapusPembayaran(id) {
            Swal.fire({
                title: 'Hapus Transaksi?',
                text: "Data keuangan akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#435ebe',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/admin/pembayaran/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Transaksi telah dihapus.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush