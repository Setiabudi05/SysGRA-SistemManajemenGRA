@extends('layouts.master')
@section('title', 'Edit Agenda Pribadi')

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
                            <li class="breadcrumb-item active text-primary" aria-current="page">Edit Agenda</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Edit Agenda Pribadi</h3>
                </div>
                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('kru.jadwal.pribadi.index') }}" class="text-muted small fw-bold text-decoration-none">
                        <i class="bi bi-chevron-left"></i> Kembali ke daftar
                    </a>
                </div>
            </div>
        </div>
        <hr class="mb-4">
    </div>

    <section class="section">
        <div class="row justify-content-center">
            {{-- Menggunakan col-12 agar form melebar penuh seperti create --}}
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0 pt-4">
                        <h5 class="fw-bold text-warning mb-0"><i class="bi bi-pencil-square me-2"></i>Form Edit Agenda</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('kru.jadwal.pribadi.update', $jadwal->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Tanggal Agenda <span class="mandatory">*</span></label>
                                        <input type="date" name="event_date" class="form-control shadow-sm"
                                            value="{{ old('event_date', $jadwal->event_date) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Tipe Agenda <span class="mandatory">*</span></label>
                                        <select id="tipeSelect" name="tipe_tugas" class="form-select shadow-sm" onchange="toggleVendorField()" required>
                                            <option value="INTERNAL" {{ $jadwal->tipe == 'INTERNAL' ? 'selected' : '' }}>Tugas Internal GRA</option>
                                            <option value="EKSTERNAL" {{ $jadwal->tipe == 'EKSTERNAL' ? 'selected' : '' }}>Tugas Vendor Lain (NCR, Eva, dll)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6" id="vendorField" style="{{ $jadwal->tipe == 'EKSTERNAL' ? '' : 'display: none;' }}">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Nama Vendor Luar</label>
                                        <input type="text" id="nama_vendor" name="nama_vendor" class="form-control shadow-sm"
                                            value="{{ old('nama_vendor', $jadwal->nama_vendor) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Nama Acara / Event <span class="mandatory">*</span></label>
                                        <input type="text" name="nama_event" class="form-control shadow-sm"
                                            value="{{ old('nama_event', $jadwal->nama_event) }}" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Catatan Tambahan</label>
                                        <textarea name="keterangan" rows="3" class="form-control shadow-sm">{{ old('keterangan', $jadwal->keterangan) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="{{ route('kru.jadwal.pribadi.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">Batal</a>
                                <button type="submit" class="btn btn-warning px-4 fw-bold shadow text-white">
                                    <i class="bi bi-save me-1"></i> Perbarui Agenda
                                </button>
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
            } else {
                $('#vendorField').fadeOut();
            }
        }
    </script>
@endpush