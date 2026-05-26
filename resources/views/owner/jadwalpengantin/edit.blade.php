@extends('layouts.master')
@section('title', 'Owner - Edit Jadwal Pengantin')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 0.375rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}" class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('owner.jadwalpengantin.index') }}" class="text-muted">Jadwal Pengantin</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Edit Jadwal</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Edit Jadwal Pengantin (Owner)</h3>
                    <p class="text-muted mb-0 small">Perbarui rincian operasional dan kendali plotting tim.</p>
                </div>
                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('owner.jadwalpengantin.index') }}" class="text-muted small fw-bold text-decoration-none">
                        <i class="bi bi-chevron-left"></i> Kembali ke daftar
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
                        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Form Plotting Jadwal</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('owner.jadwalpengantin.update', $jadwal->id) }}" method="POST" data-parsley-validate>
                            @csrf
                            @method('PUT')

                            <div class="row mt-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal Awal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_awal" class="form-control shadow-sm"
                                        value="{{ old('tanggal_awal', $jadwal->tanggal_awal ? \Carbon\Carbon::parse($jadwal->tanggal_awal)->format('Y-m-d') : '') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal Akhir (Opsional)</label>
                                    <input type="date" name="tanggal_akhir" class="form-control shadow-sm"
                                        value="{{ old('tanggal_akhir', $jadwal->tanggal_akhir ? \Carbon\Carbon::parse($jadwal->tanggal_akhir)->format('Y-m-d') : '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nama Pengantin <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control shadow-sm"
                                        value="{{ old('nama', $jadwal->nama) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Pilih Paket <span class="text-danger">*</span></label>
                                    <select name="paket_id" class="form-select shadow-sm" required>
                                        <option value="">-- Pilih Paket --</option>
                                        @foreach($pakets as $paket)
                                            <option value="{{ $paket->id }}" {{ old('paket_id', $jadwal->paket_id) == $paket->id ? 'selected' : '' }}>{{ $paket->nama_paket }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Alamat Acara <span class="text-danger">*</span></label>
                                    <textarea name="alamat" rows="2" class="form-control shadow-sm" required>{{ old('alamat', $jadwal->alamat) }}</textarea>
                                </div>

                                <div class="col-12"><hr class="my-3"></div>

                                {{-- PLOTTING KRU BERDASARKAN VARIABEL SPESIFIK --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Asisten MUA (Bisa > 1)</label>
                                    <select name="asisten[]" id="asisten-select" class="form-select select2 shadow-sm" multiple="multiple">
                                        @foreach($kruAsisten as $u)
                                            @php
                                                $selectedAsisten = $jadwal->asisten ? explode(',', $jadwal->asisten) : [];
                                            @endphp
                                            <option value="{{ $u->name }}" {{ in_array($u->name, $selectedAsisten) ? 'selected' : '' }}>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Fotografer (FG)</label>
                                    <select name="fg" class="form-select shadow-sm">
                                        <option value="">-- Pilih FG --</option>
                                        @foreach($kruFG as $u)
                                            <option value="{{ $u->name }}" {{ old('fg', $jadwal->fg) == $u->name ? 'selected' : '' }}>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Layos / Dekorasi</label>
                                    <select name="layos" class="form-select shadow-sm">
                                        <option value="">-- Pilih Layos --</option>
                                        @foreach($kruLayos as $u)
                                            <option value="{{ $u->name }}" {{ old('layos', $jadwal->layos) == $u->name ? 'selected' : '' }}>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- FIELD CATATAN KHUSUS --}}
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Keterangan / Catatan Khusus Pelanggan</label>
                                    <textarea name="keterangan" rows="3" class="form-control shadow-sm" 
                                        placeholder="Contoh: Akad jam 8 pagi, ada adat Pedang Pora, dll.">{{ old('keterangan', $jadwal->keterangan) }}</textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <a href="{{ route('owner.jadwalpengantin.index') }}" class="btn btn-secondary px-4 fw-bold">Batal</a>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                        <i class="bi bi-save me-1"></i> Simpan Perubahan
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $('#asisten-select').select2({
                theme: 'bootstrap-5',
                placeholder: "-- Pilih Asisten --",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush