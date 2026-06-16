@extends('layouts.user')

@section('title', 'Riwayat Pembayaran & Cicilan')

@section('content')
    <div class="page-heading mb-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="fw-bold text-dark"><i class="bi bi-cash-stack me-2 text-primary"></i>Riwayat Pembayaran & Cicilan</h3>
                <p class="text-muted small">Selesaikan pembayaran DP awal atau cicilan Anda secara otomatis menggunakan Midtrans Sandbox.</p>
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $b)
                                @php
                                    $hargaBersih = (float) $b->package_price;
                                    
                                    // PERBAIKAN: Hitung nominal real-time murni dari status success/lunas di tabel pembayarans
                                    $totalTerbayar = \DB::table('pembayarans')
                                        ->where('pesanan_id', $b->id)
                                        ->where(function ($q) {
                                            $q->whereRaw('LOWER(status_pembayaran) = ?', ['success'])
                                              ->orWhereRaw('LOWER(status_pembayaran) = ?', ['lunas'])
                                              ->orWhereNull('status_pembayaran');
                                        })
                                        ->sum('jumlah_bayar');
                                        
                                    $sisaTagihan = $hargaBersih - $totalTerbayar;
                                    if($sisaTagihan < 0) $sisaTagihan = 0;
                                    
                                    $statusMain = strtoupper($b->status);
                                @endphp
                                <tr class="border-bottom">
                                    <td class="fw-bold text-primary">#GRA-{{ $b->id }}</td>
                                    <td>
                                        <div class="fw-bold text-dark mb-0">{{ $b->package_name }}</div>
                                        <div class="small text-secondary">{{ $b->customer_name }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light-secondary text-dark fw-bold border">
                                            {{ date('d M Y', strtotime($b->event_date)) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">Rp {{ number_format($hargaBersih, 0, ',', '.') }}</td>
                                    
                                    <td class="fw-bold text-danger">
                                        @if($sisaTagihan <= 0)
                                            <span class="text-success fw-bold d-block"><i class="bi bi-check-circle-fill me-1"></i>LUNAS</span>
                                        @else
                                            Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    
                                    <td class="text-center">
                                        @if($statusMain == 'PENDING')
                                            <span class="badge bg-warning text-dark px-3 py-2 fw-bold">BELUM DP (PENDING)</span>
                                        @elseif($statusMain == 'MENUNGGU VERIFIKASI')
                                            <span class="badge bg-info text-white px-3 py-2 fw-bold"><i class="bi bi-hourglass-split me-1"></i>MENUNGGU VERIFIKASI</span>
                                        @elseif(in_array($statusMain, ['CONFIRMED', 'TERKONFIRMASI', 'SUCCESS', 'LUNAS']))
                                            <span class="badge bg-success px-3 py-2 fw-bold"><i class="bi bi-shield-fill-check me-1"></i>SAH (TERKONFIRMASI)</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2 fw-bold">{{ $statusMain }}</span>
                                        @endif
                                    </td>
                                    
                                    <td class="text-center">
                                        @if($sisaTagihan > 0)
                                            <button class="btn btn-primary btn-sm rounded-pill pay-button px-3 shadow-sm" 
                                                data-id="{{ $b->id }}" 
                                                data-status="{{ strtolower($statusMain) }}"
                                                data-sisa="{{ $sisaTagihan }}">
                                                <i class="bi bi-credit-card-2-front-fill me-1"></i> 
                                                {{ $statusMain == 'PENDING' ? 'Bayar DP Awal' : 'Bayar Cicilan' }}
                                            </button>
                                        @else
                                            <span class="badge rounded-pill bg-light-success text-success px-3 py-2 fw-bold border border-success-subtle">
                                            <i class="bi bi-patch-check-fill me-1"></i>Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Belum ada data transaksi aktif.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') ?? env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.pay-button').forEach(button => {
                button.onclick = function () {
                    const bookingId = this.getAttribute('data-id');
                    const statusBooking = this.getAttribute('data-status');
                    const sisaTagihan = parseInt(this.getAttribute('data-sisa'));

                    if (statusBooking === 'pending') {
                        Swal.fire({
                            title: 'Pembayaran DP Awal',
                            text: 'Sesuai ketentuan GRA, DP awal untuk mengunci tanggal adalah minimal Rp 5.000.000',
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Lanjut ke Midtrans',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                panggilMidtransSnap(bookingId, 5000000);
                            }
                        });
                    } 
                    else {
                        Swal.fire({
                            title: 'Masukkan Nominal Cicilan',
                            html: `<p class="text-muted small mb-2">Sisa tagihan Anda saat ini: <b>Rp ${sisaTagihan.toLocaleString('id-ID')}</b></p>`,
                            input: 'number',
                            inputAttributes: {
                                min: 100000,
                                max: sisaTagihan,
                                step: 10000
                            },
                            inputPlaceholder: 'Masukkan jumlah uang (Contoh: 2000000)',
                            showCancelButton: true,
                            confirmButtonText: 'Proses Pembayaran',
                            cancelButtonText: 'Batal',
                            preConfirm: (value) => {
                                if (!value || value <= 0) {
                                    Swal.showValidationMessage('Nominal harus lebih besar dari 0!');
                                } else if (value > sisaTagihan) {
                                    Swal.showValidationMessage('Nominal tidak boleh melebihi sisa tagihan!');
                                }
                                return value;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                panggilMidtransSnap(bookingId, result.value);
                            }
                        });
                    }
                };
            });

            function panggilMidtransSnap(bookingId, nominal) {
                Swal.fire({
                    title: 'Menyiapkan Pembayaran...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });

                fetch(`{{ route('user.checkout.process') }}?id=${bookingId}&nominal_pembayaran=${nominal}`)
                    .then(res => {
                        if (!res.ok) throw new Error('Gagal mengambil token pembayaran');
                        return res.json();
                    })
                    .then(data => {
                        Swal.close();

                        snap.pay(data.snap_token, {
                            onSuccess: function (result) {
                                Swal.fire('Sukses!', 'Uang cicilan Anda berhasil dikirim ke sistem.', 'success')
                                    .then(() => location.reload());
                            },
                            onPending: function (result) {
                                Swal.fire('Menunggu Pembayaran', 'Nomor Virtual Account/QRIS berhasil dibuat. Silakan selesaikan tagihan Anda.', 'info')
                                    .then(() => location.reload());
                            },
                            onError: function (result) {
                                Swal.fire('Gagal!', 'Terjadi kesalahan sistem transaksi.', 'error');
                            },
                            onClose: function () {
                                Swal.fire('Informasi', 'Anda membatalkan proses pembayaran cicilan.', 'warning');
                            }
                        });
                    })
                    .catch(err => {
                        Swal.close();
                        Swal.fire('Error', 'Gagal terhubung dengan server pembayaran Midtrans.', 'error');
                    });
            }
        });
    </script>
@endpush