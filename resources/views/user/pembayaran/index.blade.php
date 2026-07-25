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
        
        {{-- KOTAK INFORMASI KETENTUAN DP & CICILAN --}}
        <div class="alert shadow-sm border-0 d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; background-color: #e3f2fd; color: #055160;">
            <i class="bi bi-info-circle-fill fs-2 me-3 text-primary"></i>
            <div>
                <h6 class="fw-bold mb-1 text-primary">Informasi Ketentuan Pembayaran (DP & Cicilan)</h6>
                <ul class="mb-0 small ps-3" style="line-height: 1.6;">
                    <li><strong>Pembayaran DP Awal:</strong> Untuk <b>Paket All In</b>, DP minimal yang harus dibayarkan adalah <b>Rp 5.000.000</b>. Untuk paket selain All In (Biasa), DP minimal adalah <b>Rp 2.000.000</b>.</li>
                    <li><strong>Sistem Cicilan:</strong> Setelah DP awal dibayarkan (status menjadi Terkonfirmasi), Anda dapat melakukan pembayaran <b>cicilan secara fleksibel</b>. Nominal cicilan bisa Anda input sendiri saat menekan tombol "Bayar".</li>
                    <li><strong>Pelunasan:</strong> Seluruh sisa tagihan harap dilunasi sebelum atau sesuai dengan tenggat waktu kesepakatan acara Anda.</li>
                </ul>
            </div>
        </div>

        <div class="card shadow-sm border-0 adaptive-card bg-white" style="border-radius: 12px;">
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
                                    $hargaBersih = (float) ($b->paket->harga ?? 0) + (float) $b->addOns->sum('harga');
                                    $totalTerbayar = $b->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
                                    $sisaTagihan = max(0, $hargaBersih - $totalTerbayar);
                                    $statusMain = strtoupper($b->status);
                                    $lastPaymentId = $b->pembayarans->last()?->id;
                                @endphp
                                <tr class="border-bottom">
                                    <td class="fw-bold text-primary align-top">#GRA-{{ $b->id }}</td>
                                    <td class="align-top">
                                        <div class="fw-bold text-dark mb-0">{{ $b->paket->nama_paket ?? 'Paket Tidak Ditemukan' }}</div>
                                        <div class="small text-secondary">
                                            <i class="bi bi-person-heart me-1"></i> {{ $b->bride_groom_name }}
                                            @if($b->addOns && $b->addOns->isNotEmpty())
                                                <div class="mt-1">
                                                    @foreach($b->addOns as $add)
                                                        <span class="badge bg-light text-primary border rounded-pill small p-1 px-2 me-1">{{ $add->nama_item }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        @if($b->pembayarans->isNotEmpty())
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-light text-primary border fw-bold px-2 py-0 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#detailRiwayat{{ $b->id }}" aria-expanded="false" style="font-size: 0.7rem;">
                                                    <i class="bi bi-list-check me-1"></i> Lihat Rincian Pembayaran ({{ $b->pembayarans->count() }})
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center align-top">
                                        <span class="badge bg-light-secondary text-dark fw-bold border">{{ date('d M Y', strtotime($b->event_date)) }}</span>
                                    </td>
                                    <td class="fw-bold text-dark align-top">Rp {{ number_format($hargaBersih, 0, ',', '.') }}</td>
                                    <td class="fw-bold text-danger align-top">
                                        @if($sisaTagihan <= 0)
                                            <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>LUNAS</span>
                                        @else
                                            Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="text-center align-top">
                                        @if($statusMain == 'PENDING')
                                            <span class="badge bg-warning text-dark px-3 py-2 fw-bold">BELUM DP</span>
                                        @elseif(in_array($statusMain, ['CONFIRMED', 'TERKONFIRMASI', 'SUCCESS', 'LUNAS']))
                                            <span class="badge bg-success px-3 py-2 fw-bold"><i class="bi bi-shield-fill-check me-1"></i>Terkonfirmasi</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2 fw-bold">{{ $statusMain }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-top">
                                        @if($sisaTagihan > 0)
                                            <button class="btn btn-primary btn-sm pay-button shadow-sm" 
                                                data-id="{{ $b->id }}" 
                                                data-status="{{ $b->status }}" 
                                                data-sisa="{{ $sisaTagihan }}"
                                                data-paket="{{ strtolower($b->paket->nama_paket ?? 'biasa') }}">
                                                Bayar
                                            </button>
                                        @endif
                                    </td>
                                    <td class="text-center align-top">
                                        @if($lastPaymentId)
                                            <button type="button" class="btn btn-sm btn-outline-info upload-bukti-btn" data-id="{{ $lastPaymentId }}" title="Upload Bukti">
                                                <i class="bi bi-cloud-arrow-up"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                @if($b->pembayarans->isNotEmpty())
                                    <tr>
                                        <td colspan="8" class="p-0 border-0">
                                            <div class="collapse bg-light p-3 border-bottom" id="detailRiwayat{{ $b->id }}">
                                                <div class="small fw-bold text-dark mb-2"><i class="bi bi-info-circle text-primary me-1"></i> Rincian Peruntukan Pembayaran / Cicilan:</div>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered bg-white mb-0 text-secondary" style="font-size: 0.8rem;">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="text-center">No</th>
                                                                <th>Tanggal Bayar</th>
                                                                <th>Keterangan / Peruntukan</th>
                                                                <th class="text-end">Jumlah Nominal</th>
                                                                <th class="text-center">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($b->pembayarans as $idx => $pay)
                                                                <tr>
                                                                    <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                                                                    <td>{{ date('d M Y, H:i', strtotime($pay->created_at)) }}</td>
                                                                    <td>
                                                                        @if($pay->keterangan)
                                                                            {{ $pay->keterangan }}
                                                                        @else
                                                                            <span class="badge {{ $idx == 0 ? 'bg-primary' : 'bg-info text-dark' }} bg-opacity-10 border border-{{ $idx == 0 ? 'primary' : 'info' }} px-2 py-1">
                                                                                {{ $idx == 0 ? 'Pembayaran DP Awal' : 'Pembayaran Cicilan ke-' . $idx }}
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-end fw-bold text-dark">Rp {{ number_format($pay->jumlah_bayar, 0, ',', '.') }}</td>
                                                                    <td class="text-center">
                                                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><i class="bi bi-check2-all me-1"></i>Sukses</span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada data transaksi aktif.</td></tr>
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
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">Upload Bukti Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <label class="form-label small fw-bold text-muted">Pilih File Bukti Transfer (JPG/PNG)</label>
                        <input type="file" name="bukti_transfer" class="form-control form-control-lg" required accept="image/*">
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="bi bi-cloud-arrow-up me-2"></i>Simpan Bukti</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') ?? env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Pesanan Berhasil!', text: 'Silakan lanjutkan pembayaran.', confirmButtonColor: '#3085d6' });
            @endif

            @if(session('success_bukti'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Bukti pembayaran berhasil diupload.', confirmButtonColor: '#28a745' });
            @endif

            document.body.addEventListener('click', function (e) {
                if (e.target.closest('.upload-bukti-btn')) {
                    const btn = e.target.closest('.upload-bukti-btn');
                    const id = btn.getAttribute('data-id');
                    document.getElementById('formUpload').action = `/user/pembayaran/upload-bukti/${id}`;
                    new bootstrap.Modal(document.getElementById('modalUploadBukti')).show();
                }

                if (e.target.closest('.pay-button')) {
                    const btn = e.target.closest('.pay-button');
                    const bookingId = btn.getAttribute('data-id');
                    const statusBooking = btn.getAttribute('data-status').toLowerCase();
                    const sisaTagihan = parseInt(btn.getAttribute('data-sisa'));
                    const namaPaket = btn.getAttribute('data-paket');

                    const isAllIn = namaPaket.includes('all in') || namaPaket.includes('all-in');
                    const nominalDP = isAllIn ? 5000000 : 2000000;

                    if (statusBooking === 'pending') {
                        Swal.fire({
                            title: 'Pembayaran DP Awal',
                            html: `Paket yang Anda pilih adalah <b>${namaPaket.toUpperCase()}</b>.<br>Sistem mewajibkan pembayaran DP (Down Payment) awal sebesar:<br><h3 class="text-primary mt-3 mb-3">Rp ${nominalDP.toLocaleString('id-ID')}</h3>`,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: '<i class="bi bi-wallet2 me-1"></i> Bayar DP Sekarang',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#0d6efd'
                        }).then((res) => { if (res.isConfirmed) panggilMidtransSnap(bookingId, nominalDP); });
                    } else {
                        Swal.fire({
                            title: 'Bayar Cicilan',
                            html: `Sisa Tagihan Anda saat ini adalah <b>Rp ${sisaTagihan.toLocaleString('id-ID')}</b>.<br>Masukkan nominal cicilan yang ingin Anda bayarkan:`,
                            input: 'text',
                            inputValue: sisaTagihan.toLocaleString('id-ID'),
                            showCancelButton: true,
                            confirmButtonText: 'Bayar Cicilan',
                            confirmButtonColor: '#198754',
                            didOpen: () => {
                                const input = Swal.getInput();
                                
                                // Membuat container agar berada persis di tengah popup dengan lebar yang pas
                                input.style.display = 'none'; // Sembunyikan input asli sementara
                                
                                const customWrapper = document.createElement('div');
                                customWrapper.className = 'd-flex align-items-center justify-content-center mx-auto mt-2';
                                customWrapper.style.maxWidth = '320px';
                                customWrapper.innerHTML = `
                                    <span class="px-3 py-2 bg-light border border-end-0 fw-bold text-secondary rounded-start" style="font-size: 1.1rem;">Rp</span>
                                    <input type="text" id="swal-rupiah-input" class="form-control text-center fw-bold rounded-end" style="font-size: 1.2rem; height: 45px;" value="${sisaTagihan.toLocaleString('id-ID')}">
                                `;
                                
                                input.parentNode.insertBefore(customWrapper, input);
                                
                                const customInput = document.getElementById('swal-rupiah-input');
                                customInput.focus();
                                customInput.setSelectionRange(customInput.value.length, customInput.value.length);

                                customInput.addEventListener('input', function () {
                                    let value = this.value.replace(/[^,\d]/g, '');
                                    let split = value.split(',');
                                    let sisa = split[0].length % 3;
                                    let rupiah = split[0].substr(0, sisa);
                                    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                                    if (ribuan) {
                                        let separator = sisa ? '.' : '';
                                        rupiah += separator + ribuan.join('.');
                                    }
                                    this.value = rupiah;
                                    input.value = rupiah; // Sinkronkan ke input asli SweetAlert
                                });
                            },
                            preConfirm: () => {
                                const customInput = document.getElementById('swal-rupiah-input');
                                const inputVal = customInput.value.replace(/\./g, '');
                                const v = parseInt(inputVal);
                                
                                if (!v || v <= 0 || v > sisaTagihan) {
                                    Swal.showValidationMessage(`Nominal harus di antara Rp 1 - Rp ${sisaTagihan.toLocaleString('id-ID')}`);
                                    return false;
                                }
                                return v;
                            }
                        }).then((res) => { 
                            if (res.isConfirmed) {
                                panggilMidtransSnap(bookingId, res.value); 
                            }
                        });
                    }
                }
            });

            function panggilMidtransSnap(bookingId, nominal) {
                Swal.fire({ title: 'Menyiapkan Pembayaran...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                fetch(`{{ route('user.checkout.process') }}?id=${bookingId}&nominal_pembayaran=${nominal}`)
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();
                        snap.pay(data.snap_token, {
                            onSuccess: () => location.reload(),
                            onPending: () => location.reload(),
                            onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan sistem saat memproses.', 'error')
                        });
                    });
            }
        });
    </script>
@endpush