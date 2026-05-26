@extends('layouts.master')
@section('title', 'Tambah Jadwal Pengantin')

@push('css')
    {{-- Memastikan jarak konten rapat ke atas dan gaya visual sinkron --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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
                            <li class="breadcrumb-item active text-primary" aria-current="page">Tambah Jadwal</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Tambah Jadwal Pengantin</h3>
                    <p class="text-muted mb-0 small">Input rincian jadwal operasional dan penugasan tim baru.</p>
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
                        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Form Tambah Jadwal</h5>
                    </div>
                    <div class="card-body">
                        {{-- Action diarahkan ke store dan method POST murni tanpa @method('PUT') --}}
                        <form action="{{ route('admin.jadwalpengantin.store') }}" method="POST"
                            data-parsley-validate>
                            @csrf

                            <div class="row mt-3">
                                {{-- Section 1: Waktu --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="tanggal_awal">Tanggal Awal <span
                                                class="mandatory">*</span></label>
                                        <input type="date" id="tanggal_awal" name="tanggal_awal"
                                            class="form-control shadow-sm @error('tanggal_awal') is-invalid @enderror"
                                            value="{{ old('tanggal_awal') }}"
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
                                            value="{{ old('tanggal_akhir') }}">
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
                                            placeholder="Contoh: Rina & Andi" value="{{ old('nama') }}"
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
                                                <option value="{{ $paket->id }}" {{ old('paket_id') == $paket->id ? 'selected' : '' }}>
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
                                            required>{{ old('alamat') }}</textarea>
                                        @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Section 3: Personel Tim --}}
                                {{-- BAGIAN DROPDOWN KRU (Struktur Sesuai Contoh Edit Kamu) --}}
                                <div class="row">
                                    {{-- Dropdown Khusus Asisten --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Asisten MUA (Bisa > 1)</label>
                                        <select name="asisten[]" id="asisten-select" class="form-select select2 shadow-sm"
                                            multiple="multiple">
                                            @foreach($kruAsisten as $u)
                                                {{-- Menghapus pengecekan $jadwal agar tidak memicu Undefined Variable --}}
                                                <option value="{{ $u->name }}" {{ (is_array(old('asisten')) && in_array($u->name, old('asisten'))) ? 'selected' : '' }}>
                                                    {{ $u->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Dropdown Khusus Fotografer --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Fotografer (FG)</label>
                                        <select name="fg" class="form-select shadow-sm">
                                            <option value="">-- Pilih FG --</option>
                                            @foreach($kruFG as $u)
                                                <option value="{{ $u->name }}" {{ old('fg') == $u->name ? 'selected' : '' }}>
                                                    {{ $u->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Dropdown Khusus Layos --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Layos / Dekorasi</label>
                                        <select name="layos" class="form-select shadow-sm">
                                            <option value="">-- Pilih Layos --</option>
                                            @foreach($kruLayos as $u)
                                                <option value="{{ $u->name }}" {{ old('layos') == $u->name ? 'selected' : '' }}>
                                                    {{ $u->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label for="keterangan" class="form-label fw-bold">Keterangan Acara / Catatan
                                            Khusus</label>
                                        <textarea id="keterangan" name="keterangan" rows="3"
                                            class="form-control shadow-sm @error('keterangan') is-invalid @enderror"
                                            placeholder="Contoh: Akad jam 09.00 pagi, ada adat Pedang Pora, dll.">{{ old('keterangan') }}</textarea>
                                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
    {{-- Menyamakan urutan eksekusi script penonaktif konflik persis seperti edit milikmu --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://parsleyjs.org/dist/parsley.min.js"></script>
    <script src="{{ asset('assets/admin/static/js/pages/parsley.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Sesuai dengan logika pemutus konflik bawaan template di file edit
            if ($('#asisten-select').hasClass("select2-hidden-accessible")) {
                $('#asisten-select').select2('destroy');
            }

            // Aktifkan Select2 murni hanya untuk ID asisten-select
            $('#asisten-select').select2({
                theme: 'bootstrap-5',
                placeholder: "-- Pilih Asisten --",
                allowClear: true,
                width: '100%'
            });
        });
    </script>

    {{-- Include SweetAlert bawaan --}}
    @include('sweetalert::alert')
@endpush