@extends('layouts.master')

@section('title', 'Edit Jadwal Layos')

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
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.jadwallayos.index') }}"
                                    class="text-muted">Jadwal Layos</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Edit Jadwal</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Edit Jadwal Layos</h3>
                    <p class="text-muted mb-0 small">Perbarui penanggung jawab layos pada jadwal yang dipilih.</p>
                </div>
                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.jadwallayos.index') }}" class="text-muted small fw-bold text-decoration-none">
                        <i class="bi bi-chevron-left"></i> Kembali ke daftar layos
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
                        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Form Perbarui Layos
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.jadwallayos.update', $jadwal->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row mt-3">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold" for="jadwal_pengantin_id">Tanggal Jadwal <span
                                                class="mandatory">*</span></label>
                                        <select name="jadwal_pengantin_id" id="jadwal_pengantin_id"
                                            class="form-select shadow-sm @error('jadwal_pengantin_id') is-invalid @enderror"
                                            required>
                                            @foreach ($jadwals as $jp)
                                                <option value="{{ $jp->id }}"
                                                    data-bulan="{{ \Carbon\Carbon::parse($jp->tanggal_awal)->translatedFormat('F') }}"
                                                    data-nama="{{ $jp->nama }}" data-paket="{{ $jp->paket->nama_paket ?? '' }}"
                                                    {{ old('jadwal_pengantin_id', $jadwal->jadwal_pengantin_id) == $jp->id ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::parse($jp->tanggal_awal)->translatedFormat('d F Y') }}
                                                    ({{ $jp->nama }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('jadwal_pengantin_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Field Sinkronisasi Otomatis (bg-light dihapus) --}}
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Bulan</label>
                                        <input type="text" id="bulan" class="form-control shadow-sm" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Paket</label>
                                        <input type="text" id="paket" class="form-control shadow-sm" readonly>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Nama Pengantin</label>
                                        <input type="text" id="nama" class="form-control shadow-sm" readonly>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold" for="layos">Pilih Layos <span
                                                class="mandatory">*</span></label>
                                        <select name="layos" id="layos"
                                            class="form-select shadow-sm @error('layos') is-invalid @enderror" required>
                                            <option value="">-- Pilih Layos --</option>
                                            @foreach (['Iswanto', 'Dimas', 'Raisya'] as $item)
                                                <option value="{{ $item }}" {{ old('layos', $jadwal->layos) == $item ? 'selected' : '' }}>{{ $item }}</option>
                                            @endforeach
                                        </select>
                                        @error('layos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="{{ route('admin.jadwallayos.index') }}"
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

                            {{-- Input Hidden untuk menjaga jejak filter --}}
                            <input type="hidden" name="last_bulan" value="{{ request('f_bulan') }}">
                            <input type="hidden" name="last_tahun" value="{{ request('f_tahun') }}">
                        </form>
                    </div>
                </div>
            </div>
    </section>
@endsection

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function updateJadwalInfo() {
            let selected = $('#jadwal_pengantin_id').find(':selected');
            if (selected.val() != "") {
                $('#bulan').val(selected.data('bulan') || '');
                $('#nama').val(selected.data('nama') || '');
                $('#paket').val(selected.data('paket') || '');
            }
        }

        $(document).ready(function () {
            $('#jadwal_pengantin_id').on('change', updateJadwalInfo);
            updateJadwalInfo(); // Load data saat halaman pertama kali dibuka
        });
    </script>
@endpush