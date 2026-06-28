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
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.jadwalgown.index') }}"
                                    class="text-muted">Jadwal Gown</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Lengkapi Rincian</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Lengkapi Rincian Gown</h3>
                    <p class="text-muted mb-0 small">Integrasi otomatis dari Jadwal Pengantin:
                        <strong>{{ $pengantin->nama }}</strong>
                    </p>
                </div>

                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.jadwalgown.index') }}" class="text-muted small fw-bold text-decoration-none">
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
                        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Form Rincian Gown</h5>
                    </div>
                    <div class="card-body">
                        {{-- Action mengarah ke update dengan ID pengantin sebagai anchor --}}
                        <form action="{{ route('admin.jadwalgown.update', $pengantin->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mt-3">
                                {{-- Field Terkunci --}}
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Rujukan Jadwal Pengantin</label>
                                    <input type="text" class="form-control shadow-sm"
                                        value="{{ \Carbon\Carbon::parse($pengantin->tanggal_awal)->translatedFormat('d F Y') }} - {{ $pengantin->nama }}"
                                        readonly>
                                    <input type="hidden" name="jadwal_pengantin_id" value="{{ $pengantin->id }}">
                                </div>
                                {{-- Tambahkan ID yang sesuai agar JavaScript bisa menemukannya --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-muted small">Bulan</label>
                                    <input type="text" id="bulan" class="form-control shadow-sm"
                                        value="{{ $pengantin->bulan }}" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-muted small">Tahun</label>
                                    <input type="text" id="tahun" class="form-control shadow-sm"
                                        value="{{ $pengantin->tahun }}" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-muted small">Paket</label>
                                    <input type="text" id="paket" class="form-control shadow-sm"
                                        value="{{ $pengantin->paket->nama_paket ?? '-' }}" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-muted small">Paket</label>
                                    <input type="text" class="form-control shadow-sm"
                                        value="{{ $pengantin->paket->nama_paket ?? '-' }}" readonly>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold" for="gown">Nama Gown / Rincian <span
                                            class="text-danger">*</span></label>
                                    {{-- $jadwal adalah objek Gown (bisa baru, bisa lama) --}}
                                    <textarea name="gown" id="gown" class="form-control shadow-sm" rows="4"
                                        placeholder="Masukkan detail gown..." required>{{ $jadwal->gown }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <a href="{{ route('admin.jadwalgown.index') }}"
                                    class="btn btn-secondary shadow-sm px-4 fw-bold">Batal</a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow">Simpan Rincian</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        function updateJadwalInfo() {
            var selected = $('#jadwal_pengantin_id').find(':selected');
            $('#bulan').val(selected.data('bulan'));
            $('#tahun').val(selected.data('tahun'));
            $('#nama').val(selected.data('nama'));
            $('#alamat').val(selected.data('alamat'));
            $('#paket').val(selected.data('paket'));
        }
        // Tidak perlu panggil updateJadwalInfo() di sini karena nilai sudah diisi PHP
        $('#jadwal_pengantin_id').change(updateJadwalInfo);
    </script>
@endpush