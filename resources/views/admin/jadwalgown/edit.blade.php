@extends('layouts.master')
@section('title', 'Edit Jadwal Gown')

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
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.jadwalgown.index') }}" class="text-muted">Jadwal Gown</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Edit Jadwal</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Edit Jadwal Gown</h3>
                <p class="text-muted mb-0 small">Perbarui rincian pemakaian gown pada jadwal yang dipilih.</p>
            </div>
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.jadwalgown.index') }}" class="text-muted small fw-bold text-decoration-none">
                    <i class="bi bi-chevron-left"></i> Kembali ke daftar gown
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
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Form Perbarui Gown</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.jadwalgown.update', $jadwalGown->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="jadwal_pengantin_id">Tanggal Jadwal <span class="mandatory">*</span></label>
                                    <select name="jadwal_pengantin_id" id="jadwal_pengantin_id" class="form-select shadow-sm" required>
                                        @foreach($jadwals as $jadwal)
                                        <option value="{{ $jadwal->id }}"
                                            data-bulan="{{ \Carbon\Carbon::parse($jadwal->tanggal_awal)->translatedFormat('F') }}"
                                            data-tahun="{{ \Carbon\Carbon::parse($jadwal->tanggal_awal)->format('Y') }}"
                                            data-nama="{{ $jadwal->nama }}"
                                            data-alamat="{{ $jadwal->alamat }}"
                                            data-paket="{{ $jadwal->paket->nama_paket ?? '' }}"
                                            {{ $jadwalGown->jadwal_pengantin_id == $jadwal->id ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::parse($jadwal->tanggal_awal)->translatedFormat('d F Y') }}
                                            ({{ $jadwal->nama }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Field Otomatis (bg-light dihapus) --}}
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Bulan</label>
                                    <input type="text" id="bulan" class="form-control shadow-sm" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Tahun</label>
                                    <input type="text" id="tahun" class="form-control shadow-sm" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
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
                                    <label class="form-label fw-bold">Alamat</label>
                                    <textarea id="alamat" class="form-control shadow-sm" rows="2" readonly></textarea>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="gown">Nama Gown / Rincian <span class="mandatory">*</span></label>
                                    <textarea name="gown" id="gown" class="form-control shadow-sm" rows="3" required>{{ old('gown', $jadwalGown->gown) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.jadwalgown.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                    <i class="bi bi-arrow-repeat me-1"></i> Perbarui Jadwal
                                </button>
                            </div>
                        </div>

                        {{-- Hidden input untuk menyimpan jejak filter asal --}}
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
<script>
    function updateJadwalInfo() {
        var selected = $('#jadwal_pengantin_id').find(':selected');
        if (selected.val() != "" && selected.val() != undefined) {
            $('#bulan').val(selected.data('bulan'));
            $('#tahun').val(selected.data('tahun'));
            $('#nama').val(selected.data('nama'));
            $('#alamat').val(selected.data('alamat'));
            $('#paket').val(selected.data('paket'));
        }
    }
    $(document).ready(function() {
        updateJadwalInfo(); // Load data awal
        $('#jadwal_pengantin_id').change(updateJadwalInfo);
    });
</script>
@endpush