@extends('layouts.master')
@section('title', 'Edit Jadwal Pengantin')

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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4">
                <h5 class="fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Form Edit Jadwal</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.jadwalpengantin.update', $jadwal->id) }}" method="POST" id="editForm">
                    @csrf @method('PUT')

                    <div class="row">
                        <!-- Waktu -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Awal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_awal" class="form-control @error('tanggal_awal') is-invalid @enderror" 
                                   value="{{ old('tanggal_awal', $jadwal->tanggal_awal ? \Carbon\Carbon::parse($jadwal->tanggal_awal)->format('Y-m-d') : '') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Akhir (Opsional)</label>
                            <input type="date" name="tanggal_akhir" class="form-control" 
                                   value="{{ old('tanggal_akhir', $jadwal->tanggal_akhir ? \Carbon\Carbon::parse($jadwal->tanggal_akhir)->format('Y-m-d') : '') }}">
                        </div>

                        <!-- Identitas -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Pengantin <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                   value="{{ old('nama', $jadwal->nama) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Pilih Paket <span class="text-danger">*</span></label>
                            <select name="paket_id" class="form-select" required>
                                <option value="">-- Pilih Paket --</option>
                                @foreach($pakets as $p)
                                    <option value="{{ $p->id }}" {{ old('paket_id', $jadwal->paket_id) == $p->id ? 'selected' : '' }}>{{ $p->nama_paket }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Alamat -->
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Alamat Acara <span class="text-danger">*</span></label>
                            <textarea name="alamat" rows="2" class="form-control" required>{{ old('alamat', $jadwal->alamat) }}</textarea>
                        </div>

                        <!-- Kru -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Asisten MUA</label>
                            <select name="asisten[]" id="asisten-select" class="form-select" multiple>
                                @php $selectedAsisten = $jadwal->asisten ? explode(',', $jadwal->asisten) : []; @endphp
                                @foreach($kruAsisten as $u)
                                    <option value="{{ $u->name }}" {{ in_array($u->name, $selectedAsisten) ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Fotografer (FG)</label>
                            <select name="fg" class="form-select">
                                <option value="">-- Pilih FG --</option>
                                @foreach($kruFG as $u)
                                    <option value="{{ $u->name }}" {{ trim(old('fg', $jadwal->fg)) == trim($u->name) ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Layos / Dekorasi</label>
                            <select name="layos" class="form-select">
                                <option value="">-- Pilih Layos --</option>
                                @foreach($kruLayos as $u)
                                    <option value="{{ $u->name }}" {{ trim(old('layos', $jadwal->layos)) == trim($u->name) ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Keterangan</label>
                            <textarea name="keterangan" rows="2" class="form-control">{{ old('keterangan', $jadwal->keterangan) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('admin.jadwalpengantin.index') }}" class="btn btn-secondary px-4 fw-bold">Kembali</a>
                        <div>
                            <button type="reset" class="btn btn-light px-4 border">Reset</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow"><i class="bi bi-save me-1"></i> Perbarui Jadwal</button>
                        </div>
                    </div>
                    
                    <input type="hidden" name="last_bulan" value="{{ request('f_bulan') }}">
                    <input type="hidden" name="last_tahun" value="{{ request('f_tahun') }}">
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#asisten-select').select2({ theme: 'bootstrap-5', placeholder: "-- Pilih Asisten --", width: '100%' });
        });
    </script>
    @include('sweetalert::alert')
@endpush