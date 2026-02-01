@extends('layouts.master')

@section('title', 'Tambah Pembayaran - Griya Rias Asmara')

@push('css')
{{-- Memanggil style eksternal untuk sinkronisasi tampilan sesuai standar SysGRA --}}
<link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
<style>
    .bg-light-primary {
        background-color: rgba(67, 94, 190, 0.1) !important;
        color: #435ebe !important;
    }
</style>
@endpush

@push('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://parsleyjs.org/dist/parsley.min.js"></script>
<script>
    $(document).ready(function() {
        // Format Rupiah Otomatis saat admin mengetik nominal
        $('#jumlah_bayar').on('keyup', function() {
            let val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(val ? new Intl.NumberFormat('id-ID').format(val) : '');
        });
    });
</script>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            {{-- Sisi Kiri: Judul dan Navigasi Breadcrumb sesuai gaya Dekorasi --}}
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.pembayaran.index') }}" class="text-muted">Pembayaran</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Catat Cicilan</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2"></i>Catat Cicilan / Pelunasan</h3>
                <p class="text-muted mb-0 small">Masukkan data transaksi pembayaran dari pengantin untuk memproses DP atau pelunasan.</p>
            </div>

            {{-- POSISI 1: Navigasi Teks Halus di Pojok Kanan Atas (Lurus dengan ujung form) --}}
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.pembayaran.index') }}" class="text-muted small fw-bold text-decoration-none">
                    <i class="bi bi-chevron-left"></i> Kembali ke daftar pembayaran
                </a>
            </div>
        </div>
    </div>
    <hr class="mb-4">
</div>

<section id="form-create-pembayaran">
    <div class="row">
        {{-- col-lg-12 memastikan lebar kartu lurus dengan navigasi teks di atas --}}
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0 pt-4">
                    <h5 class="fw-bold text-primary mb-0">Form Input Pembayaran</h5>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form class="form" action="{{ route('admin.pembayaran.store') }}" method="POST" data-parsley-validate>
                            @csrf

                            <div class="row mt-3">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Pilih Pesanan Pengantin <span class="text-danger">*</span></label>
                                    <select name="booking_id" class="form-select shadow-sm @error('booking_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Nama Pengantin --</option>
                                        @foreach($list_booking as $b)
                                        <option value="{{ $b->id }}" {{ old('booking_id') == $b->id ? 'selected' : '' }}>
                                            {{ $b->bride_groom_name }} (Sisa: Rp {{ number_format($b->sisa_tagihan, 0, ',', '.') }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text text-muted small mt-2">Hanya menampilkan pesanan yang statusnya belum lunas.</div>
                                    @error('booking_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Nominal Pembayaran <span class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light fw-bold text-primary border-2">Rp</span>
                                        <input type="text" name="jumlah_bayar" id="jumlah_bayar"
                                            class="form-control fw-bold text-primary border-2 @error('jumlah_bayar') is-invalid @enderror"
                                            placeholder="0" required>
                                    </div>
                                    @error('jumlah_bayar')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-4">
                                    <label class="form-label fw-bold">Keterangan <span class="text-danger">*</span></label>
                                    <input type="text" name="keterangan"
                                        class="form-control shadow-sm border-2 @error('keterangan') is-invalid @enderror"
                                        placeholder="Contoh: Cicilan ke-2, Pelunasan, dll" value="{{ old('keterangan') }}" required>
                                    @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert bg-light-primary border-0 d-flex align-items-center mb-4">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <span class="small">Pastikan nominal yang diinput sudah sesuai dengan bukti transfer atau uang tunai yang diterima.</span>
                            </div>

                            {{-- POSISI 2: Footer Form (Kembali di kiri, Reset & Simpan di kanan) --}}
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                {{-- Tombol Kembali Fisik Sejajar di Kiri --}}
                                <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>

                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                        <i class="bi bi-save me-1"></i> Simpan Transaksi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('sweetalert::alert')
@endsection

@push('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Format Rupiah Otomatis saat mengetik
        $('#jumlah_bayar').on('keyup', function() {
            let val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(val ? new Intl.NumberFormat('id-ID').format(val) : '');
        });
    });
</script>
@endpush