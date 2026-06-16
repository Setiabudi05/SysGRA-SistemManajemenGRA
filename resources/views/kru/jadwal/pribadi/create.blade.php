@extends('layouts.master')
@section('title', 'Tambah Agenda Pribadi')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('kru.dashboard') }}" class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('kru.jadwal.pribadi.index') }}" class="text-muted">Jadwal Pribadi</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Tambah Agenda</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Tambah Agenda Pribadi</h3>
                    <p class="text-muted mb-0 small">Inputkan ketersediaan atau jadwal vendor luar Anda.</p>
                </div>

                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('kru.jadwal.pribadi.index') }}" class="text-muted small fw-bold text-decoration-none">
                        <i class="bi bi-chevron-left"></i> Kembali ke daftar jadwal
                    </a>
                </div>
            </div>
        </div>
        <hr class="mb-4">
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0 pt-4">
                        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Form Tambah Agenda</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('kru.jadwal.pribadi.store') }}" method="POST" data-parsley-validate>
                            @csrf

                            <div class="row mt-3">
                                {{-- Tanggal Agenda --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="event_date">Tanggal Agenda <span class="mandatory">*</span></label>
                                        <input type="date" id="event_date" name="event_date"
                                            class="form-control shadow-sm @error('event_date') is-invalid @enderror"
                                            value="{{ old('event_date') }}" required>
                                        @error('event_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Tipe Agenda --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="tipeSelect">Tipe Agenda <span class="mandatory">*</span></label>
                                        <select id="tipeSelect" name="tipe_tugas" class="form-select shadow-sm" onchange="toggleVendorField()" required>
                                            <option value="INTERNAL" {{ old('tipe_tugas') == 'INTERNAL' ? 'selected' : '' }}>Tugas Internal GRA</option>
                                            <option value="EKSTERNAL" {{ old('tipe_tugas') == 'EKSTERNAL' ? 'selected' : '' }}>Tugas Vendor Lain (NCR, Eva, dll)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Nama Vendor (Dinamis) --}}
                                <div class="col-md-6" id="vendorField" style="display: none;">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="nama_vendor">Nama Vendor Luar <span class="mandatory">*</span></label>
                                        <input type="text" id="nama_vendor" name="nama_vendor"
                                            class="form-control shadow-sm" placeholder="Misal: NCR / Eva Rias"
                                            value="{{ old('nama_vendor') }}">
                                    </div>
                                </div>

                                {{-- Nama Event --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="nama_event">Nama Acara / Event <span class="mandatory">*</span></label>
                                        <input type="text" id="nama_event" name="nama_event"
                                            class="form-control shadow-sm @error('nama_event') is-invalid @enderror"
                                            placeholder="Contoh: Wedding Lia & Bandi" value="{{ old('nama_event') }}" required>
                                        @error('nama_event') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Keterangan --}}
                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label for="keterangan" class="form-label fw-bold">Catatan Tambahan (Opsional)</label>
                                        <textarea id="keterangan" name="keterangan" rows="3"
                                            class="form-control shadow-sm"
                                            placeholder="Masukkan rincian tambahan jika ada...">{{ old('keterangan') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="{{ route('kru.jadwal.pribadi.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>

                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                        <i class="bi bi-save me-1"></i> Simpan Agenda
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

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function toggleVendorField() {
            const val = $('#tipeSelect').val();
            if (val === 'EKSTERNAL') {
                $('#vendorField').fadeIn();
                $('#nama_vendor').attr('required', true);
            } else {
                $('#vendorField').fadeOut();
                $('#nama_vendor').attr('required', false);
            }
        }
        $(document).ready(function() { toggleVendorField(); });
    </script>
    @include('sweetalert::alert')
@endpush