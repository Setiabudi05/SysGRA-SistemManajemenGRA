<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Resmi GRA - #{{ $pembayaran->id }}</title>
    <style>
        @page { margin: 1.5cm 1.2cm; size: A4 portrait; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333333; font-size: 9.5pt; line-height: 1.4; }
        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .kop-table td { border: none !important; padding: 0 !important; vertical-align: top; }
        .logo-txt { font-size: 36pt; font-weight: bold; color: #b8943a; letter-spacing: 3px; line-height: 0.9; }
        .brand-name { font-size: 11pt; font-weight: bold; color: #555555; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 5px; }
        .gallery-info { text-align: right; font-size: 8.5pt; color: #666666; line-height: 1.4; }
        .gallery-info .title-gallery { font-weight: bold; color: #b8943a; font-size: 9pt; letter-spacing: 1px; margin-bottom: 2px; }
        .divider-line { border-top: 1px solid #b8943a; margin-top: 15px; margin-bottom: 25px; }
        .meta-container { width: 100%; margin-bottom: 30px; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { border: none !important; padding: 6px 0 !important; font-size: 9.5pt; vertical-align: middle; }
        .meta-label { width: 14%; color: #555555; }
        .meta-value { width: 34%; font-weight: bold; border-bottom: 1px dotted #cccccc !important; color: #111111; }
        .meta-space { width: 4%; }
        table.items-table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px; }
        table.items-table th { border-top: 1px solid #111111 !important; border-bottom: 1px solid #111111 !important; padding: 10px 4px !important; font-size: 9pt; text-transform: uppercase; font-weight: bold; color: #111111; }
        table.items-table td { border-bottom: 1px solid #eeeeee !important; padding: 14px 4px !important; font-size: 9.5pt; }
        .bottom-container { width: 100%; margin-top: 15px; }
        .bank-info-box { width: 48%; float: left; font-size: 8.5pt; color: #666666; }
        .calc-box { width: 48%; float: right; }
        .calc-table { width: 100%; border-collapse: collapse; }
        .calc-table td { padding: 5px 4px; font-size: 9.5pt; border: none !important; }
        .calc-value { text-align: right; font-weight: bold; color: #111111; }
        .row-total-masuk { border-top: 1px solid #dddddd !important; border-bottom: 1px solid #111111 !important; }
        .signature-section { width: 100%; margin-top: 50px; }
        .sig-column { width: 50%; text-align: center; font-size: 9.5pt; }
        .footer-note { margin-top: 70px; text-align: center; font-size: 8.5pt; font-weight: bold; color: #b8943a; letter-spacing: 1.5px; text-transform: uppercase; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <table class="kop-table">
        <tr>
            <td>
                <div class="logo-txt">GRA</div>
                <div class="brand-name">Griya Rias Asmara</div>
            </td>
            <td class="gallery-info">
                <div class="title-gallery">NEW GALLERY GRA</div>
                Gg. Bons Royen 4 Dukuh Kendaga<br>
                Rt 02/11 Larangan - Brebes<br>
                HP: 085866659930 / 083861300111<br>
                Instagram: @griyariasasmara
            </td>
        </tr>
    </table>

    <div class="divider-line"></div>

    <div class="meta-container">
        <table class="meta-table">
            <tr>
                <td class="meta-label">Nama</td>
                <td class="meta-value">{{ $pembayaran->booking->bride_groom_name ?? $pembayaran->booking->customer_name }}</td>
                <td class="meta-space"></td>
                <td class="meta-label">No. Nota</td>
                <td class="meta-value">#GRA-{{ $pembayaran->id }}</td>
            </tr>
            <tr>
                <td class="meta-label">Tanggal Cetak</td>
                <td class="meta-value">{{ \Carbon\Carbon::parse($pembayaran->created_at)->isoFormat('D MMMM Y') }}</td>
                <td class="meta-space"></td>
                <td class="meta-label">Tgl. Acara</td>
                <td class="meta-value">{{ \Carbon\Carbon::parse($pembayaran->booking->event_date)->isoFormat('D MMMM Y') }}</td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">No</th>
                <th style="width: 67%;">Nama Item / Deskripsi Layanan</th>
                <th style="width: 25%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; color: #888;">1</td>
                <td>
                    <span style="font-weight: bold; display: block; font-size: 10pt; color: #111111;">
                        RIAS PENGANTIN PAKET {{ strtoupper($pembayaran->booking->package_name ?? 'INTIMATE WEDDING') }}
                    </span>
                    @if($pembayaran->keterangan)
                        <small style="color: #777777; display: block; margin-top: 3px; font-style: italic;">
                            Keterangan: {{ $pembayaran->keterangan }}
                        </small>
                    @endif
                </td>
                <td style="text-align: right; font-weight: bold; font-size: 10.5pt; color: #111111;">
                    Rp {{ number_format((float)($pembayaran->booking->package_price), 0, ',', '.') }}
                </td>
            </tr>
            {{-- Perulangan Add-ons --}}
            @foreach($pembayaran->booking->addOns as $index => $add)
            <tr>
                <td style="text-align: center; color: #888;">{{ $index + 2 }}</td>
                <td style="font-style: italic; color: #555;">+ {{ $add->nama_item }}</td>
                <td style="text-align: right; font-weight: bold; font-size: 9.5pt; color: #555;">
                    Rp {{ number_format((float)$add->harga, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        // Menggunakan total_harga accessor untuk perhitungan akurat
        $totalTagihanKeseluruhan = (float)$pembayaran->booking->total_harga; 
        $jumlahMasukSekarang = (float)$pembayaran->jumlah_bayar;
        $totalTerbayarSebelumnya = DB::table('pembayarans')
            ->where('pesanan_id', $pembayaran->booking->id)
            ->where('id', '<', $pembayaran->id)
            ->whereIn('status_pembayaran', ['success', 'lunas', null])
            ->sum('jumlah_bayar');

        $subTotalNotaIni = $totalTagihanKeseluruhan - $totalTerbayarSebelumnya;
        $sisaTagihanAkhir = $subTotalNotaIni - $jumlahMasukSekarang;
    @endphp

    <div class="bottom-container">
        <div class="bank-info-box">
            <div style="font-weight: bold; color: #111111; margin-bottom: 6px;">Pembayaran Resmi Melalui:</div>
            <div style="font-size: 8.5pt; color: #555555;">
                BRI: 5868-0100-8087-506<br>
                BCA: 3610-0662-07<br>
                BNI: 117-418-8281
            </div>
        </div>

        <div class="calc-box">
            <table class="calc-table">
                <tr>
                    <td style="color: #555555;">SUB TOTAL</td>
                    <td class="calc-value">Rp {{ number_format($subTotalNotaIni < 0 ? 0 : $subTotalNotaIni, 0, ',', '.') }}</td>
                </tr>
                <tr class="row-total-masuk">
                    <td style="font-weight: bold; color: #198754;">TOTAL MASUK (NOTA INI)</td>
                    <td class="calc-value" style="color: #198754;">Rp {{ number_format($jumlahMasukSekarang, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #dc3545;">SISA TAGIHAN AKHIR</td>
                    <td class="calc-value" style="color: #dc3545;">
                        {{ $sisaTagihanAkhir <= 0 ? 'LUNAS' : 'Rp ' . number_format($sisaTagihanAkhir, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <table class="signature-section">
        <tr>
            <td class="sig-column">
                <p style="margin-bottom: 50px; color: #555555;">Tanda Terima,</p>
                <p style="text-decoration: underline; font-weight: bold; color: #333333;">( .................................... )</p>
            </td>
            <td class="sig-column">
                <p style="margin-bottom: 50px; color: #555555;">Hormat Kami,</p>
                <p style="font-weight: bold; color: #b8943a; text-decoration: underline;">GRA MANAGEMENT</p>
            </td>
        </tr>
    </table>

    <div class="footer-note">Terimakasih Atas Kepercayaan Anda</div>
</body>
</html>