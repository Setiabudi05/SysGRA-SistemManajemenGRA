@extends('layouts.user')

@section('title', 'Booking Paket Pernikahan')

@section('content')
    <div class="page-heading mb-2">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h5 class="mb-0 text-primary">Booking Paket Pernikahan</h5>
                <p class="text-xs text-muted mb-0">Lengkapi data rencana hari bahagia Anda.</p>
            </div>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Booking</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        {{-- PERBAIKAN: Menampilkan Error Validasi (Penyebab reload tanpa pesan) --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible show fade">
                <h6 class="alert-heading">Terjadi Kesalahan!</h6>
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible show fade">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 adaptive-card">
            <div class="card-body p-3">
                {{-- PERBAIKAN: Gunakan route yang sudah kita definisikan di web.php --}}
                <form action="{{ route('user.booking.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        {{-- Sisi Kiri: Identitas & Media Sosial --}}
                        <div class="col-md-7 border-end">
                            <h6 class="mb-3 title-text small text-primary"><i
                                    class="bi bi-person-lines-fill me-2"></i>Identitas</h6>
                            <div class="row g-2 text-start">
                                <div class="col-6">
                                    <label class="info-label-sm">Nama Pemesan</label>
                                    <input type="text" class="form-control form-control-sm" name="customer_name"
                                        value="{{ old('customer_name', Auth::user()->name) }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="info-label-sm">Nama Pengantin</label>
                                    <input type="text" class="form-control form-control-sm border-primary-subtle"
                                        name="bride_groom_name" value="{{ old('bride_groom_name') }}" placeholder=""
                                        required>
                                </div>
                                <div class="col-6">
                                    <label class="info-label-sm">WhatsApp</label>
                                    <input type="number" class="form-control form-control-sm" name="whatsapp_number"
                                        value="{{ old('whatsapp_number') }}" placeholder="08..." required>
                                </div>
                                <div class="col-6">
                                    <label class="info-label-sm">Nama Orang Tua</label>
                                    <input type="text" class="form-control form-control-sm" name="parent_name"
                                        value="{{ old('parent_name') }}" placeholder="Nama Orang Tua">
                                </div>
                                <div class="col-6">
                                    <label class="info-label-sm">Facebook</label>
                                    <input type="text" class="form-control form-control-sm" name="facebook_name"
                                        value="{{ old('facebook_name') }}">
                                </div>
                                <div class="col-6">
                                    <label class="info-label-sm">Instagram</label>
                                    <input type="text" class="form-control form-control-sm" name="instagram_name"
                                        value="{{ old('instagram_name') }}">
                                </div>
                                <div class="col-12 mt-2">
                                    <label class="info-label-sm">Catatan Khusus</label>
                                    <textarea class="form-control form-control-sm border-primary-subtle" name="notes"
                                        rows="4" placeholder="Detail request...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Sisi Kanan: Waktu & Paket --}}
                        <div class="col-md-5 d-flex flex-column justify-content-between text-start">
                            <div>
                                <h6 class="mb-3 title-text small text-primary"><i
                                        class="bi bi-calendar-check me-2"></i>Waktu & Layanan</h6>
                                <div class="form-group mb-2">
                                    <label class="info-label-sm">Pilih Paket</label>
                                    <select class="form-select form-select-sm border-primary-subtle" name="paket_id"
                                        required>
                                        <option value="">-- Pilih Paket --</option>
                                        @foreach($pakets as $p)
                                            <option value="{{ $p->id }}" {{ old('paket_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nama_paket }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row g-2 mb-2">
                                    {{-- Ganti bagian input tanggal di form Anda dengan ini --}}
                                    <div class="col-8">
                                        <label class="info-label-sm">Tanggal Acara</label>
                                        <div class="input-group">
                                            <input type="text" id="range-date"
                                                class="form-control form-control-sm border-primary-subtle bg-white"
                                                name="event_date_range" value="{{ old('event_date_range') }}"
                                                placeholder="Klik untuk pilih tanggal" required>
                                            <span class="input-group-text bg-white border-primary-subtle py-0">
                                                <i class="bi bi-calendar-range small"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label class="info-label-sm">Durasi</label>
                                        <input type="text" id="total-duration" class="form-control form-control-sm bg-light"
                                            name="event_duration_display"
                                            value="{{ old('event_duration_display', '1 Hari') }}" readonly>
                                        {{-- Hidden input untuk menyimpan angka murni ke database --}}
                                        <input type="hidden" name="event_duration" id="db-duration"
                                            value="{{ old('event_duration', 1) }}">
                                    </div>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="info-label-sm">Alamat Lokasi</label>
                                    <textarea class="form-control form-control-sm border-primary-subtle"
                                        name="event_address" rows="3" required>{{ old('event_address') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit"
                                    class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm btn-mazer-blue">
                                    <i class="bi bi-cart-plus-fill me-2"></i> Masukkan Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @push('css')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    {{-- Script di bagian bawah --}}
    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                flatpickr("#range-date", {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    disableMobile: "true", // Memastikan UI Flatpickr muncul di Android, bukan kalender bawaan HP
                    onChange: function (selectedDates) {
                        const durationDisplay = document.getElementById('total-duration');
                        const dbDuration = document.getElementById('db-duration');

                        if (selectedDates.length === 2) {
                            const diffDays = Math.ceil(Math.abs(selectedDates[1] - selectedDates[0]) / (1000 * 60 * 60 * 24)) + 1;
                            durationDisplay.value = diffDays + " Hari";
                            dbDuration.value = diffDays;
                        } else {
                            durationDisplay.value = "1 Hari";
                            dbDuration.value = 1;
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection