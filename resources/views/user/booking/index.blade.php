@extends('layouts.user')
@section('title', 'Booking Paket Pernikahan')

@section('content')
<div class="page-content mt-4">
    <div class="card shadow-sm border-0" style="border-radius: 20px;">
        <div class="card-body p-4">
            <form action="{{ route('user.booking.store') }}" method="POST">
                @csrf
                <div class="row">
                    {{-- SISI KIRI: IDENTITAS --}}
                    <div class="col-md-7 border-end">
                        <h6 class="mb-3 fw-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i>Identitas</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small fw-bold text-muted text-uppercase">Nama Pemesan</label>
                                <input type="text" class="form-control bg-light" name="customer_name" value="{{ old('customer_name', Auth::user()->name) }}" required>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted text-uppercase">Nama Pengantin</label>
                                <input type="text" class="form-control bg-light" name="bride_groom_name" value="{{ old('bride_groom_name') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted text-uppercase">WhatsApp</label>
                                <input type="number" class="form-control bg-light" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted text-uppercase">Nama Orang Tua</label>
                                <input type="text" class="form-control bg-light" name="parent_name" value="{{ old('parent_name') }}">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted text-uppercase">Facebook</label>
                                <input type="text" class="form-control bg-light" name="facebook_name" value="{{ old('facebook_name') }}">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted text-uppercase">Instagram</label>
                                <input type="text" class="form-control bg-light" name="instagram_name" value="{{ old('instagram_name') }}">
                            </div>

                            <div class="col-8 mt-2">
                                <label class="small fw-bold text-muted text-uppercase">Tanggal Acara</label>
                                <div class="input-group">
                                    <input type="text" id="range-date" class="form-control bg-white" name="event_date_range" value="{{ old('event_date_range') }}" placeholder="Klik pilih tanggal" required>
                                    <span class="input-group-text bg-white"><i class="bi bi-calendar-range"></i></span>
                                </div>
                            </div>
                            <div class="col-4 mt-2">
                                <label class="small fw-bold text-muted text-uppercase">Durasi</label>
                                <input type="text" id="total-duration" class="form-control bg-light" name="event_duration_display" value="{{ old('event_duration_display', '1 Hari') }}" readonly>
                                <input type="hidden" name="event_duration" id="db-duration" value="{{ old('event_duration', 1) }}">
                            </div>

                            {{-- ADD-ONS --}}
                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold text-muted text-uppercase small">Item Tambahan (Add-ons)</label>
                                <select name="addons[]" id="addons-select" class="form-select shadow-sm" multiple="multiple" style="width: 100%;">
                                    @foreach($addOns as $item)
                                        <option value="{{ $item->id }}" data-desc="{{ $item->deskripsi }}">
                                            {{ $item->nama_item }} - Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="small fw-bold text-muted text-uppercase">Catatan Khusus</label>
                                <textarea class="form-control bg-light" name="notes" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- SISI KANAN: PAKET & PETA --}}
                    <div class="col-md-5">
                        <h6 class="fw-bold text-primary"><i class="bi bi-box-seam me-2"></i>Paket & Lokasi</h6>
                        <div class="form-group mb-3">
                            <label class="small fw-bold text-muted text-uppercase">Pilih Paket</label>
                            <select class="form-select bg-light" name="paket_id" required>
                                <option value="">-- Pilih Paket --</option>
                                @foreach($pakets as $p) <option value="{{ $p->id }}">{{ $p->nama_paket }}</option> @endforeach
                            </select>
                        </div>

                        <label class="small fw-bold text-muted text-uppercase">Lokasi Acara</label>
                        <div id="map" style="height: 250px; width: 100%; border-radius: 10px; border: 2px solid #e9ecef; margin-bottom: 10px;"></div>
                        
                        <div class="alert alert-light-primary p-2 mb-2 border">
                            <small class="text-primary"><i class="bi bi-info-circle me-1"></i> Klik peta untuk deteksi lokasi.</small>
                        </div>

                        <textarea class="form-control bg-light mb-3" name="event_address" id="event_address" rows="2" placeholder="Alamat otomatis..." required>{{ old('event_address') }}</textarea>
                        
                        <input type="hidden" name="latitude" id="lat" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" id="lng" value="{{ old('longitude') }}">
                        
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-cart-plus-fill me-2"></i> Masukkan Keranjang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .select2-container--bootstrap-5 .select2-selection { min-height: 38px; }
    </style>
@endpush

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#addons-select').select2({
                theme: 'bootstrap-5',
                placeholder: "-- Pilih Item Tambahan --",
                allowClear: true,
                width: '100%',
                templateResult: function (state) {
                    if (!state.id) return state.text;
                    var desc = $(state.element).data('desc');
                    return $('<span>' + state.text + '</span><br><small class="text-muted">' + (desc || 'Tidak ada deskripsi') + '</small>');
                }
            });

            var map = L.map('map').setView([-6.9147, 108.9730], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

            var marker;
            map.on('click', function (e) {
                if (marker) map.removeLayer(marker);
                marker = L.marker(e.latlng).addTo(map);
                document.getElementById('lat').value = e.latlng.lat;
                document.getElementById('lng').value = e.latlng.lng;
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                    .then(r => r.json())
                    .then(data => { document.getElementById('event_address').value = data.display_name || "Lokasi tidak ditemukan."; });
            });

            flatpickr("#range-date", {
                mode: "range", dateFormat: "Y-m-d", minDate: "today",
                onChange: function (dates) {
                    if (dates.length === 2) {
                        let diff = Math.ceil((dates[1] - dates[0]) / (1000 * 60 * 60 * 24)) + 1;
                        document.getElementById('total-duration').value = diff + " Hari";
                        document.getElementById('db-duration').value = diff;
                    }
                }
            });
            setTimeout(function () { map.invalidateSize(); }, 500);
        });
    </script>
@endpush