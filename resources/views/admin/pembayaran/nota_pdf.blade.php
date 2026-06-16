<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Resmi GRA - #{{ $pembayaran->id }}</title>
    <style>
        @page {
            margin: 1.5cm 1.2cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333333;
            font-size: 9.5pt;
            line-height: 1.4;
        }

        /* Kop Surat Mewah & Minimalis Griya Rias Asmara */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .kop-table td {
            border: none !important;
            padding: 0 !important;
            vertical-align: top;
        }

        .logo-txt {
            font-size: 36pt;
            font-weight: bold;
            color: #b8943a; /* Emas Khas GRA */
            letter-spacing: 3px;
            line-height: 0.9;
        }

        .brand-name {
            font-size: 11pt;
            font-weight: bold;
            color: #555555;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .gallery-info {
            text-align: right;
            font-size: 8.5pt;
            color: #666666;
            line-height: 1.4;
        }

        .gallery-info .title-gallery {
            font-weight: bold;
            color: #b8943a;
            font-size: 9pt;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .divider-line {
            border-top: 1px solid #b8943a;
            margin-top: 15px;
            margin-bottom: 25px;
        }

        /* Blok Metadata Model Dotted Isian Nota Fisik */
        .meta-container {
            width: 100%;
            margin-bottom: 30px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            border: none !important;
            padding: 6px 0 !important;
            font-size: 9.5pt;
            vertical-align: middle;
        }

        .meta-label {
            width: 14%;
            color: #555555;
        }

        .meta-value {
            width: 34%;
            font-weight: bold;
            border-bottom: 1px dotted #cccccc !important;
            color: #111111;
        }

        .meta-space {
            width: 4%;
        }

        /* Desain Tabel Item Transaksi Elegan Tanpa Grid Kaku */
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        table.items-table th {
            border-top: 1px solid #111111 !important;
            border-bottom: 1px solid #111111 !important;
            border-left: none !important;
            border-right: none !important;
            padding: 10px 4px !important;
            font-size: 9pt;
            text-align: left;
            text-transform: uppercase;
            font-weight: bold;
            color: #111111;
            letter-spacing: 0.5px;
        }

        table.items-table td {
            border-bottom: 1px solid #eeeeee !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            padding: 14px 4px !important;
            font-size: 9.5pt;
            vertical-align: middle;
        }

        /* Kontainer Rincian Finansial & Rekening Bawah */
        .bottom-container {
            width: 100%;
            margin-top: 15px;
        }

        .bank-info-box {
            width: 48%;
            float: left;
            font-size: 8.5pt;
            color: #666666;
            line-height: 1.5;
        }

        .bank-title {
            font-weight: bold;
            color: #111111;
            font-size: 9pt;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .calc-box {
            width: 48%;
            float: right;
        }

        .calc-table {
            width: 100%;
            border-collapse: collapse;
        }

        .calc-table td {
            padding: 5px 4px;
            font-size: 9.5pt;
            border: none !important;
        }

        .calc-label {
            text-align: left;
            color: #555555;
        }

        .calc-value {
            text-align: right;
            font-weight: bold;
            color: #111111;
        }

        .row-total-masuk {
            border-top: 1px solid #dddddd !important;
            border-bottom: 1px solid #111111 !important;
        }

        .row-total-masuk td {
            padding: 8px 4px;
        }

        /* Tanda Tangan */
        .signature-section {
            width: 100%;
            margin-top: 50px;
        }

        .sig-column {
            width: 50%;
            text-align: center;
            font-size: 9.5pt;
        }

        .footer-note {
            margin-top: 70px;
            text-align: center;
            font-size: 8.5pt;
            font-weight: bold;
            color: #b8943a;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .clear { clear: both; }
    </style>
</head>
<body>

    {{-- KOP SURAT MEWAH MINIMALIS --}}
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

    {{-- METADATA DATA PELANGGAN (DINAMIS & OTOMATIS) --}}
    <div class="meta-container">
        <table class="meta-table">
            <tr>
                <td class="meta-label">Nama</td>
                <td class="meta-value" style="text-transform: uppercase;">
                    {{ $pembayaran->booking->nama ?? $pembayaran->booking->customer_name }}
                </td>
                <td class="meta-space"></td>
                <td class="meta-label">No. Nota</td>
                <td class="meta-value">#GRA-{{ $pembayaran->id }}</td>
            </tr>
            <tr>
                <td class="meta-label">Alamat</td>
                <td class="meta-value">
                    {{ $pembayaran->booking->alamat ?? $pembayaran->booking->address ?? 'Songgom' }}
                </td>
                <td class="meta-space"></td>
                <td class="meta-label">Tanggal Cetak</td>
                <td class="meta-value">
                    {{ \Carbon\Carbon::parse($pembayaran->created_at)->isoFormat('D MMMM Y') }}
                </td>
            </tr>
            <tr>
                <td class="meta-label">Tgl. Acara</td>
                <td class="meta-value" style="color: #b8943a;">
                    {{ \Carbon\Carbon::parse($pembayaran->booking->tanggal_awal ?? $pembayaran->booking->event_date)->isoFormat('D MMMM Y') }}
                </td>
                <td class="meta-space"></td>
                <td class="meta-label"></td>
                <td class="meta-value" style="border-bottom: none !important;"></td>
            </tr>
        </table>
    </div>

    {{-- TABEL ITEM TRANSAKSI --}}
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
                        RIAS PENGANTIN PAKET {{ strtoupper($pembayaran->booking->paket->nama_paket ?? $pembayaran->booking->package_name ?? 'INTIMATE WEDDING') }}
                    </span>
                    @if($pembayaran->keterangan)
                        <small style="color: #777777; display: block; margin-top: 3px; font-style: italic;">
                            Keterangan: {{ $pembayaran->keterangan }}
                        </small>
                    @endif
                </td>
                <td style="text-align: right; font-weight: bold; font-size: 10.5pt; color: #111111;">
                    Rp {{ number_format((float)($pembayaran->booking->paket->harga ?? $pembayaran->booking->package_price ?? 15500000), 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- LOGIKA KALKULASI FINANSIAL SEQUENCE BERTAHAP (MUNDUR BERANTAI) --}}
    @php
        // 1. Dapatkan harga pokok keseluruhan paket dari database
        $hargaPaketAwal = (float)($pembayaran->booking->paket->harga ?? $pembayaran->booking->package_price ?? 15500000);
        
        // 2. Nominal dana masuk dari nota yang sedang aktif di-render saat ini
        $jumlahMasukSekarang = (float)$pembayaran->jumlah_bayar;
        
        // 3. Cari tahu total akumulasi dana yang sudah masuk dari transaksi-transaksi terdahulu (ID yang lebih kecil)
        $totalTerbayarSebelumnya = DB::table('pembayarans')
            ->where('pesanan_id', $pembayaran->booking->id)
            ->where('id', '<', $pembayaran->id)
            ->where(function($q) {
                $q->where('status_pembayaran', 'LIKE', '%success%')
                  ->orWhere('status_pembayaran', 'LIKE', '%lunas%')
                  ->orWhereNull('status_pembayaran');
            })
            ->sum('jumlah_bayar');

        // 4. Sub Total nota ini adalah sisa beban tagihan dari akumulasi riwayat transaksi masa lalu
        $subTotalNotaIni = $hargaPaketAwal - $totalTerbayarSebelumnya;
        if($subTotalNotaIni < 0) $subTotalNotaIni = 0;

        // 5. Hitung sisa akhir tagihan murni setelah pemotongan dana masuk nota saat ini
        $sisaTagihanAkhir = $subTotalNotaIni - $jumlahMasukSekarang;
        if($sisaTagihanAkhir < 0) $sisaTagihanAkhir = 0;
    @endphp

    {{-- RINGKASAN DATA REKENING & KALKULASI BERTAHAP --}}
    <div class="bottom-container">
        <div class="bank-info-box">
            <div class="bank-title">Pembayaran Resmi Melalui:</div>
            <table style="width: 100%; font-size: 8.5pt; color: #555555;">
                <tr>
                    <td style="width: 12%; font-weight: bold; padding: 2px 0;">BRI</td>
                    <td style="width: 5%; padding: 2px 0;">:</td>
                    <td style="padding: 2px 0; letter-spacing: 0.5px;">5868-0100-8087-506</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 2px 0;">BCA</td>
                    <td>:</td>
                    <td style="padding: 2px 0; letter-spacing: 0.5px;">3610-0662-07</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 2px 0;">BNI</td>
                    <td>:</td>
                    <td style="padding: 2px 0; letter-spacing: 0.5px;">117-418-8281</td>
                </tr>
            </table>
        </div>

        <div class="calc-box">
            <table class="calc-table">
                <tr>
                    <td class="calc-label">SUB TOTAL</td>
                    <td class="calc-value">Rp {{ number_format($subTotalNotaIni, 0, ',', '.') }}</td>
                </tr>
                <tr class="row-total-masuk">
                    <td class="calc-label" style="font-weight: bold; color: #198754;">TOTAL MASUK (NOTA INI)</td>
                    <td class="calc-value" style="color: #198754; font-size: 11pt;">
                        Rp {{ number_format($jumlahMasukSekarang, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="calc-label" style="font-weight: bold; color: #dc3545;">SISA TAGIHAN AKHIR</td>
                    <td class="calc-value" style="color: #dc3545; font-size: 10.5pt;">
                        {{ $sisaTagihanAkhir <= 0 ? 'LUNAS' : 'Rp ' . number_format($sisaTagihanAkhir, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    {{-- AREA TANDA TANGAN LEGALITAS --}}
    <table class="signature-section">
        <tr>
            <td class="sig-column">
                <p style="margin-bottom: 50px; color: #555555;">Tanda Terima,</p>
                <p style="text-decoration: underline; font-weight: bold; color: #333333;">( .................................... )</p>
            </td>
            <td class="sig-column">
                <p style="margin-bottom: 50px; color: #555555;">Hormat Kami,</p>
                <p style="font-weight: bold; color: #b8943a; text-decoration: underline; letter-spacing: 0.5px;">GRA MANAGEMENT</p>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Terimakasih Atas Kepercayaan Anda
    </div>

</body>
</html>