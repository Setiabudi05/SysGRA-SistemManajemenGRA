@extends('layouts.master')

@section('title', 'Edit Jadwal Dekor')

@push('css')
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
                        <li class="breadcrumb-item"><a href="{{ route('admin.jadwaldekor.index') }}" class="text-muted">Jadwal Dekor</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Edit Jadwal</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Edit Jadwal Dekor</h3>
                <p class="text-muted mb-0 small">Perbarui rincian dekorasi jadwal yang sudah ada.</p>
            </div>

            {{-- Sisi Kanan: Navigasi Teks Halus (Lurus dengan ujung form) --}}
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.jadwaldekor.index') }}" class="text-muted small fw-bold text-decoration-none">
                    <i class="bi bi-chevron-left"></i> Kembali ke daftar dekorasi
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
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Form Perbarui Dekorasi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.jadwaldekor.update', $jadwal->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="jadwal_pengantin_id">Tanggal Jadwal <span class="mandatory">*</span></label>
                                    <select name="jadwal_pengantin_id" id="jadwal_pengantin_id" class="form-select shadow-sm" required>
                                        @foreach ($jadwals as $j)
                                        <option value="{{ $j->id }}"
                                            data-bulan="{{ \Carbon\Carbon::parse($j->tanggal_awal)->translatedFormat('F') }}"
                                            data-tahun="{{ \Carbon\Carbon::parse($j->tanggal_awal)->format('Y') }}"
                                            data-nama="{{ $j->nama }}"
                                            data-alamat="{{ $j->alamat }}"
                                            data-paket="{{ $j->paket->nama_paket ?? '' }}"
                                            data-paket_id="{{ $j->paket->id ?? '' }}"
                                            {{ $jadwal->jadwal_pengantin_id == $j->id ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::parse($j->tanggal_awal)->translatedFormat('d F Y') }}
                                            ({{ $j->nama }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- FIELD OTOMATIS: Class bg-light dihapus agar transparan mengikuti tema dark/light --}}
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Bulan</label>
                                    <input type="text" name="bulan" id="bulan" class="form-control shadow-sm" value="{{ $jadwal->bulan }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Tahun</label>
                                    <input type="text" name="tahun" id="tahun" class="form-control shadow-sm" value="{{ $jadwal->tahun }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Paket</label>
                                    <input type="text" id="paket" class="form-control shadow-sm" value="{{ $jadwal->paket->nama_paket ?? '' }}" readonly>
                                    <input type="hidden" name="paket_id" id="paket_id" value="{{ $jadwal->paket_id }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="nama">Nama Pengantin <span class="mandatory">*</span></label>
                                    <input type="text" name="nama" id="nama" class="form-control shadow-sm" value="{{ $jadwal->nama }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="foto">Foto Dekor (Opsional)</label>
                                    <input type="file" name="foto" id="foto" class="form-control shadow-sm" accept="image/*">
                                    @if ($jadwal->foto)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/'.$jadwal->foto) }}" class="rounded shadow-sm img-thumbnail" width="100">
                                        <small class="text-muted d-block mt-1">Foto saat ini</small>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="alamat">Alamat <span class="mandatory">*</span></label>
                                    <textarea name="alamat" id="alamat" class="form-control shadow-sm" rows="2" required>{{ $jadwal->alamat }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="deskripsi">Deskripsi</label>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control shadow-sm" rows="3" placeholder="Tambahkan catatan khusus dekorasi...">{{ $jadwal->deskripsi }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Buttons --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.jadwaldekor.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                    <i class="bi bi-arrow-repeat me-1"></i> Perbarui Jadwal
                                </button>
                            </div>
                        </div>

                        {{-- Hidden input untuk menjaga filter --}}
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
        if (selected.val() != "") {
            $('#bulan').val(selected.data('bulan'));
            $('#tahun').val(selected.data('tahun'));
            $('#nama').val(selected.data('nama'));
            $('#alamat').val(selected.data('alamat'));
            $('#paket').val(selected.data('paket'));
            $('#paket_id').val(selected.data('paket_id'));
        }
    }
    $(document).ready(function() {
        $('#jadwal_pengantin_id').change(updateJadwalInfo);
    });
</script>
@endpush