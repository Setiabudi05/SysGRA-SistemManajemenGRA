@extends('layouts.master')

@section('title', 'Tambah Jadwal Pengantin')

@push('css')
{{-- Memanggil file CSS eksternal untuk sinkronisasi tampilan --}}
<link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
<style>
    .mandatory {
        color: red;
        font-weight: bold;
    }

    .form-label {
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        {{-- Row dengan align-items-center agar judul dan tombol sejajar secara vertikal --}}
        <div class="row align-items-center">
            {{-- Sisi Kiri: Judul dan Navigasi --}}
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.jadwalpengantin.index') }}" class="text-muted">Jadwal Pengantin</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tambah Jadwal</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Tambah Jadwal Pengantin</h3>
                <p class="text-muted mb-0 small">Input rincian jadwal operasional dan penugasan tim.</p>
            </div>

            {{-- POSISI 1: Navigasi Teks Halus di Pojok Kanan Atas (Lurus dengan ujung form) --}}
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.jadwalpengantin.index') }}" class="text-muted small fw-bold text-decoration-none">
                    <i class="bi bi-chevron-left"></i> Kembali ke daftar jadwal
                </a>
            </div>
        </div>
    </div>
    <hr class="mb-4">
</div>

<section class="section">
    <div class="row">
        {{-- col-lg-12 memastikan lebar kartu lurus dengan navigasi teks di atas --}}
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0 pt-4">
                    <h5 class="fw-bold text-primary mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Form Rincian Jadwal
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.jadwalpengantin.store') }}" method="POST" data-parsley-validate>
                        @csrf
                        <div class="row mt-3">
                            {{-- Rentang Tanggal --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tanggal_awal" class="form-label fw-bold">Tanggal Awal <span class="mandatory">*</span></label>
                                    <input type="date" id="tanggal_awal" name="tanggal_awal"
                                        class="form-control shadow-sm @error('tanggal_awal') is-invalid @enderror"
                                        value="{{ old('tanggal_awal') }}" required>
                                    @error('tanggal_awal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tanggal_akhir" class="form-label fw-bold">Tanggal Akhir (Opsional)</label>
                                    <input type="date" id="tanggal_akhir" name="tanggal_akhir"
                                        class="form-control shadow-sm @error('tanggal_akhir') is-invalid @enderror"
                                        value="{{ old('tanggal_akhir') }}">
                                    @error('tanggal_akhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Identitas & Paket --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nama" class="form-label fw-bold">Nama Pengantin <span class="mandatory">*</span></label>
                                    <input type="text" id="nama" name="nama"
                                        class="form-control shadow-sm @error('nama') is-invalid @enderror"
                                        placeholder="Contoh: Rina & Andi" value="{{ old('nama') }}" required>
                                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="paket_id" class="form-label fw-bold">Pilih Paket <span class="mandatory">*</span></label>
                                    <select id="paket_id" name="paket_id"
                                        class="form-select shadow-sm @error('paket_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Paket --</option>
                                        @foreach($pakets as $paket)
                                        <option value="{{ $paket->id }}" {{ old('paket_id') == $paket->id ? 'selected' : '' }}>
                                            {{ $paket->nama_paket }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('paket_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Alamat --}}
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="alamat" class="form-label fw-bold">Alamat Acara <span class="mandatory">*</span></label>
                                    <textarea id="alamat" name="alamat" rows="3"
                                        class="form-control shadow-sm @error('alamat') is-invalid @enderror"
                                        placeholder="Masukkan alamat lengkap lokasi acara..."
                                        required>{{ old('alamat') }}</textarea>
                                    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Personel Tim --}}
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="asisten" class="form-label fw-bold">Asisten MUA</label>
                                    <input type="text" id="asisten" name="asisten" class="form-control shadow-sm"
                                        placeholder="Nama Asisten" value="{{ old('asisten') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="fg" class="form-label fw-bold">Fotografer (FG)</label>
                                    <input type="text" id="fg" name="fg" class="form-control shadow-sm"
                                        placeholder="Nama Fotografer" value="{{ old('fg') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="layos" class="form-label fw-bold">Layos / Dekorasi</label>
                                    <input type="text" id="layos" name="layos" class="form-control shadow-sm"
                                        placeholder="Tim Layos" value="{{ old('layos') }}">
                                </div>
                            </div>
                        </div>

                        {{-- POSISI 2: Footer Form (Kembali di kiri, Reset & Simpan di kanan) --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            {{-- Tombol Kembali Fisik Sejajar di Kiri --}}
                            <a href="{{ route('admin.jadwalpengantin.index') }}"
                                class="btn btn-secondary shadow-sm px-4 fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>

                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                    <i class="bi bi-save me-1"></i> Simpan Jadwal
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
<script src="https://parsleyjs.org/dist/parsley.min.js"></script>
<script src="{{ asset('assets/admin/static/js/pages/parsley.js') }}"></script>
@include('sweetalert::alert')
@endpush