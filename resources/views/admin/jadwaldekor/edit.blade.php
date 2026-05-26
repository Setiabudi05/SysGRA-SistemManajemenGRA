@extends('layouts.master')

@section('title', 'Edit Jadwal Dekor')

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
                        <li class="breadcrumb-item"><a href="{{ route('admin.jadwaldekor.index') }}" class="text-muted">Jadwal Dekor</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Lengkapi Rincian</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Lengkapi Rincian Dekor</h3>
                <p class="text-muted mb-0 small">Integrasi otomatis dari Jadwal Pengantin: <strong>{{ $pengantin->nama }}</strong></p>
            </div>

            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.jadwaldekor.index') }}" class="text-muted small fw-bold text-decoration-none">
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
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Form Rincian Dekorasi</h5>
                </div>
                <div class="card-body">
                    {{-- Form action diarahkan ke ID Pengantin agar sinkron --}}
                    <form action="{{ route('admin.jadwaldekor.update', $pengantin->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Rujukan Jadwal Pengantin (Terkunci)</label>
                                    <input type="text" class="form-control shadow-sm" 
                                           value="{{ \Carbon\Carbon::parse($pengantin->tanggal_awal)->translatedFormat('d F Y') }} - {{ $pengantin->nama }}" readonly>
                                    <input type="hidden" name="jadwal_pengantin_id" value="{{ $pengantin->id }}">
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold text-muted small">Bulan</label>
                                    <input type="text" name="bulan" id="bulan" class="form-control shadow-sm" value="{{ $pengantin->bulan }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold text-muted small">Tahun</label>
                                    <input type="text" name="tahun" id="tahun" class="form-control shadow-sm" value="{{ $pengantin->tahun }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold text-muted small">Paket</label>
                                    <input type="text" class="form-control shadow-sm" value="{{ $pengantin->paket->nama_paket ?? '' }}" readonly>
                                    <input type="hidden" name="paket_id" value="{{ $pengantin->paket_id }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold text-muted small">Nama Pengantin</label>
                                    <input type="text" name="nama" class="form-control shadow-sm" value="{{ $pengantin->nama }}" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="foto">Foto Dokumentasi Dekorasi</label>
                                    <input type="file" name="foto" id="foto" class="form-control shadow-sm" accept="image/*">
                                    @if ($jadwal->foto)
                                    <div class="mt-2 p-2 border rounded bg-light">
                                        <img src="{{ asset('storage/'.$jadwal->foto) }}" class="rounded shadow-sm img-thumbnail" width="120">
                                        <small class="text-muted ms-2">Foto saat ini (Biarkan kosong jika tidak ingin mengubah)</small>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold text-muted small">Alamat Lokasi</label>
                                    <textarea name="alamat" class="form-control shadow-sm" rows="2" readonly>{{ $pengantin->alamat }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold" for="deskripsi">Rincian / Catatan Dekorasi <span class="mandatory">*</span></label>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control shadow-sm" rows="4" 
                                              placeholder="Masukkan detail dekorasi (Contoh: Backdrop putih, bunga segar, kursi Tiffany...)" required>{{ $jadwal->deskripsi }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.jadwaldekor.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </a>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                    <i class="bi bi-check-circle me-1"></i> Simpan Rincian
                                </button>
                            </div>
                        </div>

                        {{-- Hidden inputs untuk filter --}}
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