@extends('layouts.master')
@section('title', 'Owner - Edit Jadwal Pengantin')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple {
            max-height: 100px;
            overflow-y: auto;
        }

        .card {
            border-radius: 12px;
        }

        .status-badge {
            font-size: 0.8rem;
            font-weight: bold;
            float: right;
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
                            <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('owner.jadwalpengantin.index') }}"
                                    class="text-muted">Jadwal Pengantin</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Edit Jadwal</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Edit Jadwal Pengantin (Owner)</h3>
                    <p class="text-muted mb-0 small">Perbarui rincian operasional dan kendali plotting tim.</p>
                </div>
                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('owner.jadwalpengantin.index') }}"
                        class="text-muted small fw-bold text-decoration-none">
                        <i class="bi bi-chevron-left"></i> Kembali ke daftar
                    </a>
                </div>
            </div>
        </div>
        <hr class="mb-4">
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('owner.jadwalpengantin.update', $jadwal->id) }}" method="POST" id="editForm">
                            @csrf @method('PUT')

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tanggal Awal <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_awal" class="form-control shadow-sm"
                                        value="{{ \Carbon\Carbon::parse($jadwal->tanggal_awal)->format('Y-m-d') }}"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tanggal Akhir (Opsional)</label>
                                    <input type="date" name="tanggal_akhir" class="form-control shadow-sm"
                                        value="{{ $jadwal->tanggal_akhir ? \Carbon\Carbon::parse($jadwal->tanggal_akhir)->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nama Pengantin <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control shadow-sm"
                                        value="{{ $jadwal->nama }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pilih Paket <span class="text-danger">*</span></label>
                                    <select name="paket_id" class="form-select shadow-sm" required>
                                        @foreach($pakets as $paket)
                                            <option value="{{ $paket->id }}" {{ $jadwal->paket_id == $paket->id ? 'selected' : '' }}>{{ $paket->nama_paket }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Alamat Acara <span
                                            class="text-danger">*</span></label>
                                    <textarea name="alamat" rows="2" class="form-control shadow-sm"
                                        required>{{ $jadwal->alamat }}</textarea>
                                </div>

                                <div class="col-12">
                                    <hr>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Asisten MUA</label>
                                    <select name="asisten[]" id="asisten-select" class="form-select" multiple="multiple">
                                        @foreach($kruAsisten as $u)
                                            <option value="{{ $u->name }}" {{ in_array($u->name, explode(',', $jadwal->asisten ?? '')) ? 'selected' : '' }}>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fotografer (FG)</label>
                                    <select name="fg" class="form-select shadow-sm status-trigger" data-target="status-fg">
                                        <option value="">-- Pilih FG --</option>
                                        @foreach($kruFG as $u)
                                            <option value="{{ $u->name }}" {{ $jadwal->fg == $u->name ? 'selected' : '' }}>
                                                {{ $u->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Layos / Dekor</label>
                                    <select name="layos" class="form-select shadow-sm status-trigger"
                                        data-target="status-layos">
                                        <option value="">-- Pilih Layos --</option>
                                        @foreach($kruLayos as $u)
                                            <option value="{{ $u->name }}" {{ $jadwal->layos == $u->name ? 'selected' : '' }}>
                                                {{ $u->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Keterangan</label>
                                    <textarea name="keterangan" rows="2"
                                        class="form-control shadow-sm">{{ $jadwal->keterangan }}</textarea>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                <a href="{{ route('owner.jadwalpengantin.index') }}"
                                    class="btn btn-secondary px-4">Batal</a>
                                <button type="submit" class="btn btn-primary px-4 shadow">Simpan Perubahan</button>
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
            // Inisialisasi Select2
            $('#asisten-select').select2({ theme: 'bootstrap-5', width: '100%' });
            $('.status-trigger').select2({ theme: 'bootstrap-5', width: '100%' });

            // Event Submit Form
            $('#editForm').on('submit', function (e) {
                // 1. Jika sudah tervalidasi, biarkan form terkirim
                if ($(this).data('validated')) return true;

                e.preventDefault();
                let form = $(this);
                let tanggal = $('input[name="tanggal_awal"]').val();
                let kruTerpilih = [];

                if ($('select[name="fg"]').val()) kruTerpilih.push($('select[name="fg"]').val());
                if ($('select[name="layos"]').val()) kruTerpilih.push($('select[name="layos"]').val());
                let asisten = $('#asisten-select').val();
                if (asisten) kruTerpilih = kruTerpilih.concat(asisten);

                // Jika tidak ada kru yang dipilih, langsung simpan saja
                if (kruTerpilih.length === 0) {
                    form.data('validated', true).submit();
                    return;
                }

                // Di dalam edit.blade.php
                let promises = kruTerpilih.map(nama => {
                    return $.ajax({
                        // Sesuaikan dengan nama rute di web.php (owner.jadwalpengantin.check-kru)
                        url: "{{ route('owner.jadwalpengantin.check-kru') }}",
                        data: { nama_kru: nama, tanggal: tanggal, jadwal_id: "{{ $jadwal->id }}" }
                    });
                });

                // Jalankan pengecekan
                Promise.all(promises).then(results => {
                    if (results.some(res => res.is_busy === true)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kru Tidak Tersedia',
                            text: 'Salah satu kru yang dipilih sudah memiliki agenda di tanggal tersebut.',
                            confirmButtonText: 'Pilih Kru Lain'
                        });
                    } else {
                        form.data('validated', true).submit();
                    }
                }).catch(err => {
                    console.error("Error AJAX:", err);
                    // Jika AJAX error, tetap izinkan simpan agar owner tidak terkunci
                    form.data('validated', true).submit();
                });
            });
        });
    </script>
@endpush