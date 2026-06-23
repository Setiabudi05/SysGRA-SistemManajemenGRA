@extends('layouts.master')
@section('title', 'Edit Pesanan - ' . $booking->bride_groom_name)

@push('css')
<link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
<style>
    .form-section-title { font-size: 0.9rem; font-weight: 700; color: #435ebe; margin-bottom: 10px; padding-bottom: 4px; border-bottom: 2px solid #f0f0f0; display: flex; align-items: center; gap: 8px; }
    .card { border-radius: 10px; margin-bottom: 1rem !important; }
    .card-body { padding: 1.1rem !important; }
    .form-label { font-weight: 600; color: #556080; font-size: 0.78rem; text-transform: uppercase; margin-bottom: 3px; }
    .form-control, .form-select { font-size: 0.85rem; padding: 0.35rem 0.7rem; border-radius: 6px; }
    .form-control:read-only { background-color: #f8f9fa !important; color: #435ebe; font-weight: bold; }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.booking.index') }}" class="text-muted">Pesanan</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Edit #{{ $booking->id }}</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Edit Data Pesanan</h3>
            </div>
            <div class="col-12 col-md-6 text-md-end">
                <a href="{{ route('admin.booking.show', $booking->id) }}" class="text-muted small fw-bold text-decoration-none">
                    <i class="bi bi-chevron-left"></i> Kembali ke detail
                </a>
            </div>
        </div>
    </div>
    <hr class="mb-4">
</div>

<section class="section">
    <form id="editForm" action="{{ route('admin.booking.update', $booking->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="form-section-title"><i class="bi bi-person-badge"></i> Identitas & Acara</div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-primary">Akun Pelanggan</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">-- Pilih Akun --</option>
                                    @foreach($list_pelanggan as $user)
                                        <option value="{{ $user->id }}" data-name="{{ $user->name }}" {{ $booking->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="customer_name" id="customer_name" value="{{ $booking->customer_name }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">WhatsApp</label>
                                <input type="text" name="whatsapp_number" class="form-control" value="{{ $booking->whatsapp_number }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-primary">Nama Pasangan Pengantin</label>
                                <input type="text" name="bride_groom_name" class="form-control fw-bold" value="{{ $booking->bride_groom_name }}" required>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Acara</label>
                                <input type="date" name="event_date" class="form-control" value="{{ \Carbon\Carbon::parse($booking->event_date)->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Durasi (Hari)</label>
                                <select name="event_duration" class="form-select" required>
                                    <option value="1" {{ $booking->event_duration == 1 ? 'selected' : '' }}>1 Hari</option>
                                    <option value="2" {{ $booking->event_duration == 2 ? 'selected' : '' }}>2 Hari</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-section-title mt-3"><i class="bi bi-geo-alt"></i> Lokasi Acara</div>
                        <textarea name="event_address" class="form-control" rows="2" required>{{ $booking->event_address }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="form-section-title"><i class="bi bi-box-seam"></i> Paket & Biaya</div>
                        <div class="mb-2">
                            <label class="form-label">Paket Utama</label>
                            <select name="package_name" id="select_paket" class="form-select" required>
                                @foreach($list_paket as $p)
                                    <option value="{{ $p->nama_paket }}" data-harga="{{ $p->harga }}" {{ $booking->package_name == $p->nama_paket ? 'selected' : '' }}>
                                        {{ $p->nama_paket }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Add-ons</label>
                            <select name="add_ons[]" id="select_addons" class="form-select" multiple style="height: 100px;">
                                @foreach($list_addons as $addon)
                                    <option value="{{ $addon->id }}" data-harga="{{ $addon->harga }}" {{ $booking->addOns->contains($addon->id) ? 'selected' : '' }}>
                                        {{ $addon->nama_item }} (Rp {{ number_format($addon->harga,0,',','.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Harga (Otomatis)</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="text" name="total_harga" id="package_price" class="form-control" 
                                       value="{{ number_format($booking->total_harga, 0, ',', '.') }}" readonly required>
                            </div>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary fw-bold shadow-sm py-2">SIMPAN PERUBAHAN</button>
                            <a href="{{ route('admin.booking.show', $booking->id) }}" class="btn btn-light py-2 border">BATAL</a>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        function hitungTotal() {
            let hargaPaket = parseFloat($('#select_paket').find(':selected').data('harga')) || 0;
            let hargaAddons = 0;
            $('#select_addons option:selected').each(function() {
                hargaAddons += parseFloat($(this).data('harga')) || 0;
            });
            let total = hargaPaket + hargaAddons;
            $('#package_price').val(new Intl.NumberFormat('id-ID').format(total));
        }

        hitungTotal();
        $('#select_paket, #select_addons').on('change', hitungTotal);
        
        $('#user_id').on('change', function() {
            $('#customer_name').val($(this).find(':selected').data('name') || '');
        });

        // SweetAlert Confirmation
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Data pesanan akan diperbarui.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#435ebe',
                confirmButtonText: 'Ya, Simpan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        @if(session('swal_success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('swal_success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endpush