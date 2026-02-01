@extends('layouts.master')
@section('title', 'Edit Pesanan - ' . $booking->bride_groom_name)

@push('css')
<style>
    /* UI Ringkas agar tidak perlu banyak scroll */
    .form-section-title { font-size: 0.9rem; font-weight: 700; color: #435ebe; margin-bottom: 10px; padding-bottom: 4px; border-bottom: 2px solid #f0f0f0; display: flex; align-items: center; gap: 8px; }
    .card { border-radius: 10px; margin-bottom: 1rem !important; }
    .card-body { padding: 1.1rem !important; }
    .form-label { font-weight: 600; color: #556080; font-size: 0.78rem; text-transform: uppercase; margin-bottom: 3px; }
    .form-control, .form-select { font-size: 0.85rem; padding: 0.35rem 0.7rem; border-radius: 6px; }
    
    /* Kolom harga dikunci agar sinkron dengan Master Paket */
    .form-control:read-only { background-color: #f8f9fa !important; color: #435ebe; font-weight: bold; }
</style>
@endpush

@section('content')
<div class="page-heading mb-2">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold">Edit Data Pesanan</h3>
            <p class="text-muted small mb-0">Perbarui rincian informasi pengantin dan rincian biaya.</p>
        </div>
        <div class="col-md-6 text-end">
            <nav aria-label="breadcrumb" class="d-inline-block">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.booking.index') }}">Pesanan</a></li>
                    <li class="breadcrumb-item active">Edit #{{ $booking->id }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<section class="section">
    <form action="{{ route('admin.booking.update', $booking->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- KOLOM KIRI: IDENTITAS & ACARA --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="form-section-title"><i class="bi bi-person-badge"></i> Identitas & Acara</div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label">Nama Pemesan</label>
                                <input type="text" name="customer_name" class="form-control" value="{{ $booking->customer_name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">WhatsApp / No. Telp</label>
                                <input type="text" name="whatsapp_number" class="form-control" value="{{ $booking->whatsapp_number }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-primary">Nama Pasangan Pengantin</label>
                                <input type="text" name="bride_groom_name" class="form-control fw-bold" value="{{ $booking->bride_groom_name }}" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Pelaksanaan</label>
                                <input type="date" name="event_date" class="form-control" value="{{ \Carbon\Carbon::parse($booking->event_date)->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Durasi Acara</label>
                                <select name="duration" class="form-select" required>
                                    {{-- Menggunakan angka agar tidak error Data Truncated --}}
                                    <option value="1" {{ $booking->event_duration == 1 ? 'selected' : '' }}>1 Hari (Sehari)</option>
                                    <option value="2" {{ $booking->event_duration == 2 ? 'selected' : '' }}>2 Hari (Dua Hari)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-section-title"><i class="bi bi-share"></i> Sosial Media & Keluarga</div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Facebook</label>
                                <input type="text" name="fb_name" class="form-control" value="{{ $booking->facebook_name }}" placeholder="Username FB">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Instagram</label>
                                <input type="text" name="ig_name" class="form-control" value="{{ $booking->instagram_name }}" placeholder="@username">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nama Orang Tua</label>
                                <input type="text" name="parent_names" class="form-control" value="{{ $booking->parent_name }}">
                            </div>
                        </div>

                        <div class="form-section-title"><i class="bi bi-geo-alt"></i> Lokasi Acara</div>
                        <textarea name="event_address" class="form-control" rows="2" required>{{ $booking->event_address }}</textarea>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PAKET & BIAYA --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="form-section-title"><i class="bi bi-box-seam"></i> Paket & Biaya</div>
                        
                        <div class="mb-2">
                            <label class="form-label">Pilih Paket Utama</label>
                            <select name="package_name" id="select_paket" class="form-select" required>
                                <option value="">-- Pilih Paket --</option>
                                @foreach($list_paket as $p)
                                    {{-- Menitipkan harga di atribut data-harga untuk otomatisasi --}}
                                    <option value="{{ $p->nama_paket }}" 
                                            data-harga="{{ $p->harga }}"
                                            {{ $booking->package_name == $p->nama_paket ? 'selected' : '' }}>
                                        {{ $p->nama_paket }} ({{ $p->tahun }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label">Harga Paket Utama</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">Rp</span>
                                {{-- Readonly untuk menjaga konsistensi keuangan --}}
                                <input type="text" name="package_price" id="package_price" 
                                       class="form-control" 
                                       value="{{ number_format($booking->package_price, 0, ',', '.') }}" 
                                       readonly required>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Paket Tambahan</label>
                            <textarea name="additional_package" class="form-control" rows="2">{{ $booking->add_ons }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Khusus</label>
                            <textarea name="notes" class="form-control" rows="2">{{ $booking->notes }}</textarea>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary fw-bold shadow-sm py-2">
                                <i class="bi bi-save me-1"></i> SIMPAN PERUBAHAN
                            </button>
                            <a href="{{ route('admin.booking.show', $booking->id) }}" class="btn btn-light py-2">BATAL</a>
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
        /**
         * Logika Sinkronisasi Paket & Harga mengikuti alur Jadwal Dekor
         */
        $('#select_paket').on('change', function() {
            // Ambil atribut data-harga dari paket yang dipilih
            let harga = $(this).find(':selected').data('harga');
            
            if (harga && harga > 0) {
                // Format angka ke ribuan (Contoh: 15.000.000)
                let formatted = new Intl.NumberFormat('id-ID').format(harga);
                $('#package_price').val(formatted);
            } else {
                $('#package_price').val('0');
            }
        });
    });
</script>
@endpush