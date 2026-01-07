@extends('user.layouts.main')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0">
                <div class="card-header text-white p-4" style="background-color: #c85716;">
                    <h4 class="mb-0">Formulir Booking Wedding</h4>
                    <p class="mb-0 opacity-75">Griya Rias Asmara Management Wedding Organizer</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form id="formBooking" action="{{ url('/booking/store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="formNamaPemesan" class="form-label fw-bold">Nama Pemesan:</label>
                                <input type="text" class="form-control" id="formNamaPemesan" name="customer_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="formNoWhatsApp" class="form-label fw-bold">No. Telp/WA (Aktif):</label>
                                <input type="tel" class="form-control" id="formNoWhatsApp" name="whatsapp_number" placeholder="Contoh: 0858xxxx" required>
                            </div>

                            <div class="col-md-6">
                                <label for="formNamaPengantin" class="form-label fw-bold">Nama Pengantin (Pria/Wanita):</label>
                                <input type="text" class="form-control" id="formNamaPengantin" name="bride_groom_name">
                            </div>
                            <div class="col-md-6">
                                <label for="formNamaOrtu" class="form-label fw-bold">Nama Orang Tua:</label>
                                <input type="text" class="form-control" id="formNamaOrtu" name="parent_name">
                            </div>

                            <div class="col-md-6">
                                <label for="formNamaFB" class="form-label fw-bold">Nama Facebook:</label>
                                <input type="text" class="form-control" id="formNamaFB" name="facebook_name">
                            </div>
                            <div class="col-md-6">
                                <label for="formNamaIG" class="form-label fw-bold">Nama Instagram:</label>
                                <input type="text" class="form-control" id="formNamaIG" name="instagram_name" placeholder="@griyariasasmara">
                            </div>

                            <div class="col-12">
                                <label for="formAlamatAcara" class="form-label fw-bold">Alamat Lengkap Acara:</label>
                                <textarea class="form-control" id="formAlamatAcara" name="event_address" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="formTanggalAcara" class="form-label fw-bold">Tanggal Pelaksanaan:</label>
                                <input type="date" class="form-control" id="formTanggalAcara" name="event_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="formDurasiAcara" class="form-label fw-bold">Durasi Acara:</label>
                                <select id="formDurasiAcara" class="form-select" name="event_duration">
                                    <option value="1 Hari" selected>1 Hari (Akad + Resepsi)</option>
                                    <option value="2 Hari">2 Hari (Akad & Resepsi terpisah)</option>
                                    <option value="Lainnya">Lainnya (Tulis di catatan)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="formPaketNama" class="form-label fw-bold text-primary">Paket Pilihan:</label>
                                <input type="text" class="form-control bg-light" id="formPaketNama" name="package_name" value="{{ request('package') }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="formPaketHarga" class="form-label fw-bold text-primary">Harga Paket:</label>
                                <input type="text" class="form-control bg-light" id="formPaketHarga" name="package_price" value="{{ request('price') }}" readonly>
                            </div>

                            <div class="col-12">
                                <label for="formPaketTambahan" class="form-label fw-bold">Paket Tambahan (Add-Ons):</label>
                                <textarea class="form-control" id="formPaketTambahan" name="add_ons" rows="2"></textarea>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-bold">Syarat & Ketentuan:</label>
                                <div class="p-3 bg-light rounded shadow-sm border-start border-4 border-warning" style="font-size: 14px;">
                                    Maksimal DP 50% setelah fitting. Jika membatalkan secara sepihak, uang hangus. Perubahan jadwal maksimal 15 hari sebelum hari H.
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="formTTD" name="agreement" required>
                                    <label class="form-check-label" for="formTTD">
                                        Saya setuju dengan Syarat & Ketentuan yang berlaku.
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-lg text-white px-5" style="background-color: #c85716; border-color: #c85716;">
                                    Kirim Formulir Booking
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection