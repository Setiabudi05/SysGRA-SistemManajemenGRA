<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <div class="modal-header" style="background-color: #f8f9fa;">
                <div>
                    <h5 class="modal-title" id="bookingModalLabel">Form Booking Wedding</h5>
                    <p class="mb-0" style="font-size: 14px; color: #555;">Griya Rias Asmara Management Wedding
                        Organizer</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formBooking" action="{{ url('/booking/store') }}" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="formNamaPemesan" class="form-label">Nama Pemesan:</label>
                            <input type="text" class="form-control" id="formNamaPemesan" name="customer_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="formNoWhatsApp" class="form-label">No. Telp/WA (Aktif):</label>
                            <input type="tel" class="form-control" id="formNoWhatsApp" name="whatsapp_number"
                                placeholder="Contoh: 0858xxxx" required>
                        </div>

                        <div class="col-md-6">
                            <label for="formNamaPengantin" class="form-label">Nama Pengantin
                                (Pria/Wanita):</label>
                            <input type="text" class="form-control" id="formNamaPengantin" name="bride_groom_name">
                        </div>
                        <div class="col-md-6">
                            <label for="formNamaOrtu" class="form-label">Nama Orang Tua:</label>
                            <input type="text" class="form-control" id="formNamaOrtu" name="parent_name">
                        </div>

                        <div class="col-md-6">
                            <label for="formNamaFB" class="form-label">Nama Facebook:</label>
                            <input type="text" class="form-control" id="formNamaFB" name="facebook_name">
                        </div>
                        <div class="col-md-6">
                            <label for="formNamaIG" class="form-label">Nama Instagram:</label>
                            <input type="text" class="form-control" id="formNamaIG" name="instagram_name"
                                placeholder="Contoh: @griyariasasmara">
                        </div>

                        <div class="col-12">
                            <label for="formAlamatAcara" class="form-label">Alamat Lengkap Acara:</label>
                            <textarea class="form-control" id="formAlamatAcara" name="event_address" rows="2"
                                required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="formTanggalAcara" class="form-label">Tanggal Pelaksanaan:</label>
                            <input type="date" class="form-control" id="formTanggalAcara" name="event_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="formDurasiAcara" class="form-label">Durasi Acara:</label>
                            <select id="formDurasiAcara" class="form-select" name="event_duration">
                                <option value="1 Hari" selected>1 Hari (Akad + Resepsi)</option>
                                <option value="2 Hari">2 Hari (Akad & Resepsi terpisah)</option>
                                <option value="Lainnya">Lainnya (Tulis di catatan)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="formPaketNama" class="form-label">Paket Pilihan:</label>
                            <input type="text" class="form-control" id="formPaketNama" name="package_name" readonly
                                style="background-color: #e9ecef;">
                        </div>
                        <div class="col-md-6">
                            <label for="formPaketHarga" class="form-label">Harga Paket:</label>
                            <input type="text" class="form-control" id="formPaketHarga" name="package_price" readonly
                                style="background-color: #e9ecef;">
                        </div>

                        <div class="col-12">
                            <label for="formPaketTambahan" class="form-label">Paket Tambahan (Add-Ons):</label>
                            <textarea class="form-control" id="formPaketTambahan" name="add_ons" rows="2"
                                placeholder="Tuliskan jika ada add-ons..."></textarea>
                        </div>
                        <div class="col-12">
                            <label for="formCatatanKebaya" class="form-label">Catatan Kebaya/Gown:</label>
                            <textarea class="form-control" id="formCatatanKebaya" name="gown_notes" rows="2"
                                placeholder="Detail request ukuran, warna, atau model..."></textarea>
                        </div>
                        <div class="col-12">
                            <label for="formCatatanLain" class="form-label">Catatan Lainnya:</label>
                            <textarea class="form-control" id="formCatatanLain" name="other_notes" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="terms"><strong>Syarat & Ketentuan (Wajib
                                    Dibaca):</strong></label>
                            <div class="terms-box">
                                <p>Dengan adanya form ini telah terjadi kesepakatan antara calon pengantin dan
                                    GRIYA
                                    RIAS ASMARA MANAGEMENT maksimal DP 50% setelah fitting dan jika calon
                                    pengantin
                                    membatalkan jadwal yang sudah masuk dengan cara sepihak, apapun alasan dan
                                    keadaan
                                    Uang yang sudah masuk dianggap hangus jika ada perubahan jadwal atau
                                    permintaan
                                    property tanpa pemberitahuan paling lambat 15 hari sebelum hari H itu semua
                                    diluar
                                    tanggung jawab.</p>
                                <p class="text-end mb-0"><strong>TEAM GRIYA RIAS ASMARA MANAGEMENT</strong></p>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="setuju" id="formTTD"
                                    name="agreement" required>
                                <label class="form-check-label" for="formTTD">
                                    <strong>Persetujuan (TTD Digital):</strong> Saya menyatakan bahwa data yang
                                    saya isi adalah benar dan saya telah membaca dan setuju dengan Syarat &
                                    Ketentuan
                                    yang berlaku.
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type_="button" id="btnKirimBooking" class="btn btn-primary"
                        style="background-color: #c85716; border-color: #c85716;">Kirim Formulir
                        Booking</button>
                </div>
            </form>

        </div>
    </div>
</div>