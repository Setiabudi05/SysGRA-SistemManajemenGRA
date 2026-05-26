@extends('layouts.user')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h3 class="fw-bold text-dark"><i class="bi bi-cash-stack me-2 text-primary"></i>Riwayat Pembayaran</h3>
            <p class="text-muted small">Pantau status transaksi Virtual Account dan QRIS Anda secara real-time.</p>
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
                            <th class="py-3 text-center text-dark fw-bold">Status</th>
                            <th class="py-3 text-center text-dark fw-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $pay)
                        <tr class="border-bottom">
                            <td class="fw-bold text-primary">#GRA-{{ $pay->id }}</td>
                            <td>
                                <div class="fw-bold text-dark mb-0">{{ $pay->package_name }}</div>
                                <div class="small text-secondary">{{ $pay->customer_name }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light-secondary text-dark fw-bold border">
                                    {{ date('d M Y', strtotime($pay->event_date)) }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark fs-6">Rp {{ number_format((int)$pay->package_price, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-center">
                                @if($pay->status == 'pending')
                                    <span class="badge bg-light-warning text-dark px-3 py-2 fw-bold border-warning">
                                        <i class="bi bi-clock-history me-1 text-warning"></i> MENUNGGU PEMBAYARAN
                                    </span>
                                @elseif($pay->status == 'settlement' || $pay->status == 'success')
                                    <span class="badge bg-light-success text-success px-3 py-2 fw-bold border-success">
                                        <i class="bi bi-check-circle me-1"></i> BERHASIL LUNAS
                                    </span>
                                @else
                                    <span class="badge bg-light-danger text-danger px-3 py-2 fw-bold border-danger">
                                        {{ strtoupper($pay->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($pay->status == 'pending')
                                    <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm pay-button fw-bold" 
                                            data-id="{{ $pay->id }}">
                                        <i class="bi bi-credit-card me-1"></i> Cara Bayar
                                    </button>
                                @else
                                    <a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20sudah%20bayar%20untuk%20ID%20Booking:%20GRA-{{ $pay->id }}" 
                                       target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold">
                                        <i class="bi bi-whatsapp me-1"></i> Konfirmasi
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="opacity-50">
                                    <i class="bi bi-receipt-cutoff display-4 text-muted"></i>
                                    <p class="mt-3 text-dark fw-bold">Belum ada riwayat transaksi aktif.</p>
                                    <small class="text-muted">Silakan lakukan konfirmasi booking di menu keranjang terlebih dahulu.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('css')

@endpush

@push('js')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.pay-button').forEach(button => {
        button.onclick = function() {
            const bookingId = this.getAttribute('data-id');
            
            // Tampilkan loading saat fetch token
            Swal.fire({ title: 'Menyiapkan Pembayaran...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });

            fetch("{{ route('user.checkout.process') }}?id=" + bookingId)
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            Swal.fire('Sukses!', 'Pembayaran berhasil.', 'success').then(() => location.reload());
                        },
                        onPending: function(result) {
                            Swal.fire('Menunggu', 'Silakan selesaikan transfer Anda.', 'info');
                        }
                    });
                })
                .catch(err => {
                    Swal.fire('Error', 'Gagal menghubungi server pembayaran.', 'error');
                });
        };
    });
</script>
@endpush
@endsection