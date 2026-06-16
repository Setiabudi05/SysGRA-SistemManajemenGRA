@extends('layouts.master')
@section('title', 'Booking Baru - Griya Rias Asmara')

@push('css')
<link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
<style>
    /* UI Ringkas Sesuai Pilihan Anda */
    .form-section-title { font-size: 0.9rem; font-weight: 700; color: #435ebe; margin-bottom: 10px; padding-bottom: 4px; border-bottom: 2px solid #f0f0f0; display: flex; align-items: center; gap: 8px; }
    .card { border-radius: 10px; margin-bottom: 1rem !important; }
    .card-body { padding: 1.1rem !important; }
    .form-label { font-weight: 600; color: #556080; font-size: 0.78rem; text-transform: uppercase; margin-bottom: 3px; }
    .form-control, .form-select { font-size: 0.85rem; padding: 0.35rem 0.7rem; border-radius: 6px; }
    
    /* Style tambahan untuk kolom harga yang terkunci */
    .form-control:read-only { background-color: #f8f9fa !important; color: #435ebe; font-weight: bold; }
</style>
@endpush

@section('content')
{{-- Bagian Header yang disesuaikan dengan Gaya Dekorasi --}}
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            {{-- Sisi Kiri: Judul dan Navigasi Breadcrumb --}}
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.booking.index') }}" class="text-muted">Pesanan</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Booking Baru</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Input Pesanan Baru</h3>
                <p class="text-muted mb-0 small">Pendaftaran booking langsung di tempat untuk pengantin.</p>
            </div>

            {{-- POSISI KANAN: Navigasi Teks Halus sesuai Gaya Dekorasi --}}
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.booking.index') }}" class="text-muted small fw-bold text-decoration-none">
                    <i class="bi bi-chevron-left"></i> Kembali ke daftar pesanan
                </a>
            </div>
        </div>
    </div>
    <hr class="mb-4">
</div>

<section class="section">
    <form action="{{ route('admin.booking.store') }}" method="POST">
        @csrf
        <div class="row">
            {{-- KOLOM KIRI: IDENTITAS & ACARA (Form Tetap) --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="form-section-title"><i class="bi bi-person-plus"></i> Identitas & Acara</div>
                        <div class="row g-2 mb-3">
                            {{-- PERBAIKAN UTAMA: Mengubah input text Nama Pemesan menjadi Dropdown Akun Pelanggan --}}
                            <div class="col-md-6">
                                <label class="form-label text-primary">Hubungkan ke Akun Pelanggan</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">-- Pilih Akun User --</option>
                                    @foreach($list_pelanggan as $user)
                                        <option value="{{ $user->id }}" data-name="{{ $user->name }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Input hidden untuk tetap mengirimkan customer_name ke controller --}}
                                <input type="hidden" name="customer_name" id="customer_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">WhatsApp / No. Telp</label>
                                <input type="number" name="whatsapp_number" class="form-control" placeholder="08xxxxxxxx" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-primary">Nama Pasangan Pengantin</label>
                                <input type="text" name="bride_groom_name" class="form-control fw-bold" placeholder="Contoh: Umi & Mujiono" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Pelaksanaan</label>
                                <input type="date" name="event_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Durasi Acara</label>
                                <select name="duration" class="form-select" required>
                                    <option value="1">1 Hari (Sehari)</option>
                                    <option value="2">2 Hari (Dua Hari)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-section-title"><i class="bi bi-share"></i> Sosial Media & Keluarga</div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Facebook</label>
                                <input type="text" name="fb_name" class="form-control" placeholder="Username FB">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Instagram</label>
                                <input type="text" name="ig_name" class="form-control" placeholder="@username">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nama Orang Tua</label>
                                <input type="text" name="parent_names" class="form-control" placeholder="Ayah & Ibu">
                            </div>
                        </div>

                        <div class="form-section-title mt-3"><i class="bi bi-geo-alt"></i> Lokasi Acara</div>
                        <textarea name="event_address" class="form-control" rows="2" required placeholder="Alamat lengkap lokasi acara..."></textarea>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PAKET & BIAYA (Form Tetap) --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="form-section-title"><i class="bi bi-box-seam"></i> Paket & Biaya</div>
                        
                        <div class="mb-2">
                            <label class="form-label">Pilih Paket Utama</label>
                            <select name="package_name" id="select_paket" class="form-select" required>
                                <option value="" data-harga="0">-- Pilih Paket --</option>
                                @foreach($list_paket as $p)
                                    <option value="{{ $p->nama_paket }}" data-harga="{{ $p->harga }}">
                                        {{ $p->nama_paket }} ({{ $p->tahun }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label">Harga Paket Utama</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="text" name="package_price" id="package_price" class="form-control" readonly required placeholder="0">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Layanan Tambahan</label>
                            <textarea name="additional_package" class="form-control" rows="2" placeholder="Hiburan, dll"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Khusus</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary fw-bold shadow-sm py-2">SIMPAN PESANAN</button>
                            <button type="reset" class="btn btn-light py-2 border">RESET FORM</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@push('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Logika Sinkronisasi Dropdown User ke Input Hidden customer_name
        $('#user_id').on('change', function() {
            let selectedName = $(this).find(':selected').data('name');
            if (selectedName) {
                $('#customer_name').val(selectedName);
            } else {
                $('#customer_name').val('');
            }
        });

        // Logika Sinkronisasi Paket & Harga
        $('#select_paket').on('change', function() {
            let harga = $(this).find(':selected').data('harga');
            if (harga && harga > 0) {
                let formatted = new Intl.NumberFormat('id-ID').format(harga);
                $('#package_price').val(formatted);
            } else {
                $('#package_price').val('0');
            }
        });
    });
</script>
@endpush