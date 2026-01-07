@extends('layouts.master')

@section('title', 'Tambah Pesanan Baru')

@push('css')
    {{-- Memastikan gaya visual sinkron dengan modul lainnya --}}
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            {{-- Sisi Kiri: Judul dan Navigasi --}}
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.booking.index') }}" class="text-muted">Manajemen Pesanan</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tambah Pesanan</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Tambah Pesanan Offline</h3>
                <p class="text-muted mb-0 small">Input data pesanan wedding yang dilakukan secara langsung/offline.</p>
            </div>

            {{-- Sisi Kanan: Navigasi Teks Halus --}}
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
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0 pt-4">
                    <h5 class="fw-bold text-primary mb-0">Formulir Internal Booking Wedding</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.booking.store') }}" method="POST">
                        @csrf
                        <div class="row mt-3">
                            {{-- Baris 1: Nama & WhatsApp --}}
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label fw-bold">Nama Pemesan <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control shadow-sm @error('customer_name') is-invalid @enderror" 
                                    value="{{ old('customer_name') }}" required>
                                @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="whatsapp_number" class="form-label fw-bold">No. Telp/WA (Aktif) <span class="text-danger">*</span></label>
                                <input type="number" name="whatsapp_number" id="whatsapp_number" class="form-control shadow-sm @error('whatsapp_number') is-invalid @enderror" 
                                    placeholder="Contoh: 0858xxxx" value="{{ old('whatsapp_number') }}" required>
                                @error('whatsapp_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Baris 2: Nama Pengantin & Nama Orang Tua --}}
                            <div class="col-md-6 mb-3">
                                <label for="bride_groom_name" class="form-label fw-bold">Nama Pengantin (Pria/Wanita)</label>
                                <input type="text" name="bride_groom_name" id="bride_groom_name" class="form-control shadow-sm" 
                                    placeholder="Misal: Andi & Bella" value="{{ old('bride_groom_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="parent_name" class="form-label fw-bold">Nama Orang Tua</label>
                                <input type="text" name="parent_name" id="parent_name" class="form-control shadow-sm" value="{{ old('parent_name') }}">
                            </div>

                            {{-- Baris 3: Sosmed --}}
                            <div class="col-md-6 mb-3">
                                <label for="facebook_name" class="form-label fw-bold">Nama Facebook</label>
                                <input type="text" name="facebook_name" id="facebook_name" class="form-control shadow-sm" value="{{ old('facebook_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="instagram_name" class="form-label fw-bold">Nama Instagram</label>
                                <input type="text" name="instagram_name" id="instagram_name" class="form-control shadow-sm" 
                                    placeholder="@..." value="{{ old('instagram_name') }}">
                            </div>

                            {{-- Baris 4: Tanggal & Durasi --}}
                            <div class="col-md-6 mb-3">
                                <label for="event_date" class="form-label fw-bold">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                                <input type="date" name="event_date" id="event_date" class="form-control shadow-sm @error('event_date') is-invalid @enderror" 
                                    value="{{ old('event_date') }}" required>
                                @error('event_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="event_duration" class="form-label fw-bold">Durasi Acara</label>
                                <select name="event_duration" id="event_duration" class="form-select shadow-sm">
                                    <option value="1 Hari" {{ old('event_duration') == '1 Hari' ? 'selected' : '' }}>1 Hari (Akad + Resepsi)</option>
                                    <option value="2 Hari" {{ old('event_duration') == '2 Hari' ? 'selected' : '' }}>2 Hari (Akad & Resepsi Terpisah)</option>
                                    <option value="Lainnya" {{ old('event_duration') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            {{-- Baris 5: Paket & Harga --}}
                            <div class="col-md-6 mb-3">
                                <label for="package_name" class="form-label fw-bold text-primary">Paket Pilihan <span class="text-danger">*</span></label>
                                <input type="text" name="package_name" id="package_name" class="form-control shadow-sm" 
                                    placeholder="Contoh: Paket Gold" value="{{ old('package_name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="package_price" class="form-label fw-bold text-primary">Harga Paket <span class="text-danger">*</span></label>
                                <input type="text" name="package_price" id="package_price" class="form-control shadow-sm" 
                                    placeholder="Contoh: Rp. 15.000.000" value="{{ old('package_price') }}" required>
                            </div>

                            {{-- Baris 6: Alamat --}}
                            <div class="col-12 mb-3">
                                <label for="event_address" class="form-label fw-bold">Alamat Lengkap Acara <span class="text-danger">*</span></label>
                                <textarea name="event_address" id="event_address" class="form-control shadow-sm @error('event_address') is-invalid @enderror" 
                                    rows="3" required>{{ old('event_address') }}</textarea>
                                @error('event_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Baris 7: Add-Ons --}}
                            <div class="col-12 mb-3">
                                <label for="add_ons" class="form-label fw-bold">Paket Tambahan (Add-Ons)</label>
                                <textarea name="add_ons" id="add_ons" class="form-control shadow-sm" rows="2">{{ old('add_ons') }}</textarea>
                            </div>
                        </div>

                        {{-- Footer Action Buttons --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.booking.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                    <i class="bi bi-save me-1"></i> Simpan Pesanan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection