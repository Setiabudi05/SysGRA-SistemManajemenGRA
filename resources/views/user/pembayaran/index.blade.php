@extends('layouts.user')

@section('title', 'Riwayat Pembayaran & Cicilan')

@section('content')
    <div class="page-heading mb-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="fw-bold text-dark"><i class="bi bi-cash-stack me-2 text-primary"></i>Riwayat Pembayaran & Cicilan
                </h3>
                <p class="text-muted small">Selesaikan pembayaran DP awal atau cicilan Anda secara otomatis menggunakan
                    Midtrans Sandbox.</p>
            </div>
            <div class="col-12 col-md-6 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 justify-content-md-end">
                        <li class="breadcrumb-item"><a href="{{ url('user/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pembayaran</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="card shadow-sm border-0 adaptive-card bg-white">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-pembayaran">
                        <thead>
                            <tr class="text-uppercase small text-muted border-bottom">
                                <th class="py-3 text-dark fw-bold">ID Transaksi</th>
                                <th class="py-3 text-dark fw-bold">Paket & Pelanggan</th>
                                <th class="py-3 text-center text-dark fw-bold">Tgl Acara</th>
                                <th class="py-3 text-dark fw-bold">Total Tagihan</th>
                                <th class="py-3 text-dark fw-bold text-danger">Sisa Tagihan</th>
                                <th class="py-3 text-center text-dark fw-bold">Status Utama</th>
                                <th class="py-3 text-center text-dark fw-bold">Aksi</th>
                                <th class="py-3 text-center text-dark fw-bold">Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $b)
                                @php
                                    // HARGA BERSIH = Harga Paket Saja + Total Add-ons (Durasi TIDAK dipakai)
                                    $hargaBersih = (float) ($b->paket->harga ?? 0) + (float) $b->addOns->sum('harga');

                                    $totalTerbayar = $b->pembayarans
                                        ->whereIn('status_pembayaran', ['success', 'lunas', null])
                                        ->sum('jumlah_bayar');

                                    $sisaTagihan = max(0, $hargaBersih - $totalTerbayar);
                                    $statusMain = strtoupper($b->status);
                                    $lastPaymentId = $b->pembayarans->last()?->id;
                                @endphp
                                <tr class="border-bottom">
                                    <td class="fw-bold text-primary">#GRA-{{ $b->id }}</td>
                                    <td>
                                        <div class="fw-bold text-dark mb-0">{{ $b->package_name }}</div>
                                        <div class="small text-secondary">
                                            <i class="bi bi-person-heart me-1"></i> {{ $b->bride_groom_name }}
                                            @if($b->addOns && $b->addOns->isNotEmpty())
                                                <div class="mt-1">
                                                    @foreach($b->addOns as $add)
                                                        <span
                                                            class="badge bg-light text-primary border rounded-pill small p-1 px-2 me-1">{{ $add->nama_item }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-light-secondary text-dark fw-bold border">{{ date('d M Y', strtotime($b->event_date)) }}</span>
                                    </td>
                                    <td class="fw-bold text-dark">Rp {{ number_format($hargaBersih, 0, ',', '.') }}</td>
                                    <td class="fw-bold text-danger">
                                        @if($sisaTagihan <= 0)
                                            <span class="text-success fw-bold"><i
                                                    class="bi bi-check-circle-fill me-1"></i>LUNAS</span>
                                        @else
                                            Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($statusMain == 'PENDING')
                                            <span class="badge bg-warning text-dark px-3 py-2 fw-bold">BELUM DP</span>
                                        @elseif(in_array($statusMain, ['CONFIRMED', 'TERKONFIRMASI', 'SUCCESS', 'LUNAS']))
                                            <span class="badge bg-success px-3 py-2 fw-bold"><i
                                                    class="bi bi-shield-fill-check me-1"></i>SAH (TERKONFIRMASI)</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2 fw-bold">{{ $statusMain }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($sisaTagihan > 0)
                                            <button type="button"
                                                class="btn btn-primary btn-sm rounded-pill pay-button px-3 shadow-sm"
                                                data-id="{{ $b->id }}" data-status="{{ strtolower($statusMain) }}"
                                                data-sisa="{{ $sisaTagihan }}">
                                                <i class="bi bi-credit-card-2-front-fill me-1"></i> Bayar
                                            </button>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($lastPaymentId)
                                            <button type="button" class="btn btn-sm btn-outline-info upload-bukti-btn"
                                                data-id="{{ $lastPaymentId }}">
                                                <i class="bi bi-cloud-arrow-up"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data transaksi aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Upload --}}
    <div class="modal fade" id="modalUploadBukti" tabindex="-1">
        <div class="modal-dialog">
            <form id="formUpload" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="bukti_transfer" class="form-control" required accept="image/*">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Bukti</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') ?? env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Pesanan Berhasil!',
                    text: 'Silakan lanjutkan pembayaran.',
                    confirmButtonColor: '#3085d6'
                });
            @endif

            // Alert 2: Muncul hanya saat upload bukti sukses
            @if(session('success_bukti'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Bukti pembayaran berhasil diupload.',
                    confirmButtonColor: '#28a745'
                });
            @endif
            // Event untuk tombol upload
            document.body.addEventListener('click', function (e) {
                if (e.target.closest('.upload-bukti-btn')) {
                    const btn = e.target.closest('.upload-bukti-btn');
                    const id = btn.getAttribute('data-id');
                    document.getElementById('formUpload').action = `/user/pembayaran/upload-bukti/${id}`;
                    new bootstrap.Modal(document.getElementById('modalUploadBukti')).show();
                }

                // Event untuk tombol bayar (Midtrans)
                if (e.target.closest('.pay-button')) {
                    const btn = e.target.closest('.pay-button');
                    const bookingId = btn.getAttribute('data-id');
                    const statusBooking = btn.getAttribute('data-status');
                    const sisaTagihan = parseInt(btn.getAttribute('data-sisa'));

                    if (statusBooking === 'pending') {
                        Swal.fire({
                            title: 'Pembayaran DP Awal',
                            text: 'Minimal DP Rp 5.000.000',
                            icon: 'info',
                            showCancelButton: true
                        }).then((res) => { if (res.isConfirmed) panggilMidtransSnap(bookingId, 5000000); });
                    } else {
                        Swal.fire({
                            title: 'Nominal Cicilan',
                            input: 'number',
                            showCancelButton: true,
                            preConfirm: (v) => { if (!v || v <= 0 || v > sisaTagihan) Swal.showValidationMessage('Cek nominal!'); return v; }
                        }).then((res) => { if (res.isConfirmed) panggilMidtransSnap(bookingId, res.value); });
                    }
                }
            });

            function panggilMidtransSnap(bookingId, nominal) {
                Swal.fire({ title: 'Menyiapkan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                fetch(`{{ route('user.checkout.process') }}?id=${bookingId}&nominal_pembayaran=${nominal}`)
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();
                        snap.pay(data.snap_token, {
                            onSuccess: () => location.reload(),
                            onPending: () => location.reload(),
                            onError: () => Swal.fire('Gagal!', 'Kesalahan sistem.', 'error')
                        });
                    });
            }
        });
    </script>
@endpush