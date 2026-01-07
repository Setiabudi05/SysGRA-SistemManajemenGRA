@extends('layouts.master')
@section('title', 'Edit Jadwal Pengantin')

@push('css')
    {{-- Memastikan jarak konten rapat ke atas dan gaya visual sinkron --}}
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                {{-- Sisi Kiri: Judul dan Breadcrumb --}}
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.jadwalpengantin.index') }}"
                                    class="text-muted">Jadwal Pengantin</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Edit Jadwal</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Edit Jadwal Pengantin</h3>
                    <p class="text-muted mb-0 small">Perbarui rincian jadwal operasional dan penugasan tim.</p>
                </div>

                {{-- Sisi Kanan: Navigasi Teks Halus (Lurus dengan ujung form) --}}
                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.jadwalpengantin.index') }}"
                        class="text-muted small fw-bold text-decoration-none">
                        <i class="bi bi-chevron-left"></i> Kembali ke daftar jadwal
                    </a>
                </div>
            </div>
        </div>
        <hr class="mb-4">
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-12"> {{-- Lebar penuh agar lurus dengan navigasi di atas --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0 pt-4">
                        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Form Edit Jadwal</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.jadwalpengantin.update', $jadwal->id) }}" method="POST"
                            data-parsley-validate>
                            @csrf
                            @method('PUT')

                            <div class="row mt-3">
                                {{-- Section 1: Waktu --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="tanggal_awal">Tanggal Awal <span
                                                class="mandatory">*</span></label>
                                        <input type="date" id="tanggal_awal" name="tanggal_awal"
                                            class="form-control shadow-sm @error('tanggal_awal') is-invalid @enderror"
                                            value="{{ old('tanggal_awal', $jadwal->tanggal_awal ? \Carbon\Carbon::parse($jadwal->tanggal_awal)->format('Y-m-d') : '') }}"
                                            required>
                                        @error('tanggal_awal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="tanggal_akhir">Tanggal Akhir
                                            (Opsional)</label>
                                        <input type="date" id="tanggal_akhir" name="tanggal_akhir"
                                            class="form-control shadow-sm @error('tanggal_akhir') is-invalid @enderror"
                                            value="{{ old('tanggal_akhir', $jadwal->tanggal_akhir ? \Carbon\Carbon::parse($jadwal->tanggal_akhir)->format('Y-m-d') : '') }}">
                                        @error('tanggal_akhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Section 2: Identitas & Lokasi --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="nama">Nama Pengantin <span
                                                class="mandatory">*</span></label>
                                        <input type="text" id="nama" name="nama"
                                            class="form-control shadow-sm @error('nama') is-invalid @enderror"
                                            placeholder="Contoh: Rina & Andi" value="{{ old('nama', $jadwal->nama) }}"
                                            required>
                                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="paket_id">Pilih Paket <span
                                                class="mandatory">*</span></label>
                                        <select id="paket_id" name="paket_id"
                                            class="form-select shadow-sm @error('paket_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Paket --</option>
                                            @foreach($pakets as $paket)
                                                <option value="{{ $paket->id }}" {{ old('paket_id', $jadwal->paket_id) == $paket->id ? 'selected' : '' }}>
                                                    {{ $paket->nama_paket }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('paket_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="alamat">Alamat Acara <span
                                                class="mandatory">*</span></label>
                                        <textarea id="alamat" name="alamat" rows="3"
                                            class="form-control shadow-sm @error('alamat') is-invalid @enderror"
                                            placeholder="Masukkan alamat lengkap lokasi acara..."
                                            required>{{ old('alamat', $jadwal->alamat) }}</textarea>
                                        @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Section 3: Personel Tim --}}
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="asisten">Asisten MUA</label>
                                        <input type="text" id="asisten" name="asisten" class="form-control shadow-sm"
                                            placeholder="Nama Asisten" value="{{ old('asisten', $jadwal->asisten) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="fg">Fotografer (FG)</label>
                                        <input type="text" id="fg" name="fg" class="form-control shadow-sm"
                                            placeholder="Nama Fotografer" value="{{ old('fg', $jadwal->fg) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="layos">Layos / Dekorasi</label>
                                        <input type="text" id="layos" name="layos" class="form-control shadow-sm"
                                            placeholder="Tim Layos" value="{{ old('layos', $jadwal->layos) }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Action Buttons --}}
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                {{-- Tombol Kembali Fisik di Kiri --}}
                                <a href="{{ route('admin.jadwalpengantin.index') }}"
                                    class="btn btn-secondary shadow-sm px-4 fw-bold">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>

                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                        <i class="bi bi-arrow-repeat me-1"></i> Perbarui Jadwal
                                    </button>
                                </div>
                            </div>
                            {{-- Tambahkan ini untuk menyimpan jejak filter --}}
                            <input type="hidden" name="last_bulan" value="{{ request('f_bulan') }}">
                            <input type="hidden" name="last_tahun" value="{{ request('f_tahun') }}">
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