<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Jadwal Pengantin</title>
    <style>
        @page {
            margin: 1.2cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.4;
        }

        /* Layout KOP Surat Resmi Super Simetris (Tabel 3 Kolom) */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .kop-table td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }

        .kop-logo-side {
            width: 70px;
        }

        .kop-text-side {
            text-align: center;
        }

        /* Susunan kotak HTML Logo */
        .pixel-logo {
            border-collapse: collapse;
            margin: 0 auto;
        }

        .pixel-logo td {
            width: 8px !important;
            height: 8px !important;
            padding: 0 !important;
            border: none !important;
        }

        .bg-dark {
            background-color: #2c3e50 !important;
        }

        .bg-gold {
            background-color: #d4af37 !important;
        }

        .kop-text-side h2 {
            margin: 0;
            font-size: 17pt;
            letter-spacing: 2px;
            color: #000;
            text-transform: uppercase;
            font-weight: bold;
        }

        .kop-text-side p {
            margin: 3px 0;
            font-size: 8.5pt;
            color: #555;
        }

        .double-line {
            border-top: 2px solid #222;
            border-bottom: 0.5px solid #222;
            height: 3px;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        /* Judul Laporan */
        .report-title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Box Informasi Ringkas */
        .info-box {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .info-box td {
            border: none !important;
            padding: 4px 0 !important;
            vertical-align: top;
            font-size: 9pt;
        }

        /* Tabel Data Utama */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table.data-table th {
            background-color: #f8f9fa;
            border: 1px solid #999999 !important;
            padding: 8px 4px !important;
            font-size: 8.5pt;
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
        }

        table.data-table td {
            border: 1px solid #cccccc !important;
            padding: 8px 5px !important;
            font-size: 8.5pt;
            vertical-align: middle;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>

<body>

    {{-- KOP SURAT SYMETRICAL CENTER --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo-side">
                <table class="pixel-logo">
                    <tr>
                        <td class="bg-gold"></td>
                        <td class="bg-gold"></td>
                        <td class="bg-gold"></td>
                        <td class="bg-gold"></td>
                        <td class="bg-gold"></td>
                    </tr>
                    <tr>
                        <td class="bg-gold"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="bg-gold"></td>
                        <td></td>
                        <td class="bg-dark"></td>
                        <td class="bg-dark"></td>
                        <td class="bg-dark"></td>
                    </tr>
                    <tr>
                        <td class="bg-gold"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="bg-dark"></td>
                    </tr>
                    <tr>
                        <td class="bg-gold"></td>
                        <td class="bg-gold"></td>
                        <td class="bg-gold"></td>
                        <td class="bg-gold"></td>
                        <td class="bg-dark"></td>
                    </tr>
                </table>
            </td>

            <td class="kop-text-side">
                <h2>GRIYA RIAS ASMARA</h2>
                <p>Alamat: Gg.Bons Royen 4 Dukuh kendaga Rt 02/11 Kec Larangan, Kabupaten Brebes, Jawa Tengah</p>
                <p>Telp/WA: 085866659930 / 08386130011 | Email: griyariasasmara@gmail.com</p>
            </td>

            <td class="kop-logo-side"></td>
        </tr>
    </table>

    <div class="double-line"></div>

    <div class="report-title">JADWAL OPERASIONAL PENGANTIN</div>

    {{-- METADATA LAPORAN --}}
    <table class="info-box">
        <tr>
            <td style="width: 15%;">Periode Jadwal</td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;" class="fw-bold">{{ $bulan ?? 'Semua' }} {{ $tahun ?? '' }}</td>

            <td style="width: 15%;">Tanggal Cetak</td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;">{{ date('d F Y') }}</td>
        </tr>
        <tr>
            <td>Jumlah Jadwal</td>
            <td>:</td>
            <td class="fw-bold">{{ count($jadwal) }} Acara</td>

            <td>Cetak Oleh</td>
            <td>:</td>
            <td class="fw-bold" style="text-transform: uppercase;">Admin (SysGRA)</td>
        </tr>
    </table>

    {{-- DATA TABLE UTAMA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 15%;">TANGGAL</th>
                <th style="width: 18%;">NAMA PENGANTIN</th>
                <th style="width: 28%;">ALAMAT LOKASI ACARA</th>
                <th style="width: 16%;">PAKET LAYANAN</th>
                <th style="width: 6%;">ASN</th>
                <th style="width: 6%;">FG</th>
                <th style="width: 6%;">LYS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $i => $row)
                @php
                    $alamatMentah = $row->alamat;

                    // Otomatis memotong nomor HP dari string alamat (menggunakan regex pintar)
                    preg_match_all('/08[0-9]{8,13}/', $alamatMentah, $matches);
                    $noHpList = $matches[0] ?? [];

                    // Bersihkan string alamat dari buntutan nomor HP biar ga duplikat teks panjang
                    $alamatBersih = trim(preg_replace('/08[0-9]{8,13}/', '', $alamatMentah));
                    $alamatBersih = rtrim($alamatBersih, ' /,-'); 
                @endphp
                <tr>
                    <td class="text-center fw-bold" style="color: #555;">{{ $i + 1 }}</td>
                    <td class="text-center" style="font-size: 8pt;">
                        @if(!empty($row->tanggal_awal))
                            {{ \Carbon\Carbon::parse($row->tanggal_awal)->translatedFormat('d F Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="fw-bold" style="color: #435ebe; font-size: 8.5pt;">{{ $row->nama }}</td>

                    <td class="text-left" style="line-height: 1.3;">
                        <span style="font-weight: bold; font-size: 8pt; display: block; color: #111;">
                            {{ $alamatBersih }}
                        </span>

                        {{-- Render otomatis nomor HP di bawah baris teks alamat dengan warna kontras --}}
                        @if(count($noHpList) > 0)
                            <small
                                style="color: #2c3e50; display: block; font-size: 7.5pt; font-weight: bold; margin-top: 3px;">
                                <span style="color: #25d366;">▶</span> HP: {{ implode(' / ', $noHpList) }}
                            </small>
                        @elseif($row->whatsapp_number)
                            <small
                                style="color: #2c3e50; display: block; font-size: 7.5pt; font-weight: bold; margin-top: 3px;">
                                <span style="color: #25d366;">▶</span> HP: {{ $row->whatsapp_number }}
                            </small>
                        @endif
                    </td>

                    <td class="text-left" style="font-size: 8pt;">{{ $row->paket->nama_paket ?? '-' }}</td>
                    <td class="text-center" style="font-size: 8pt;">{{ $row->asisten ?? '-' }}</td>
                    <td class="text-center" style="font-size: 8pt;">{{ $row->fg ?? '-' }}</td>
                    <td class="text-center" style="font-size: 8pt;">{{ $row->layos ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 25px; color: #6c757d; font-style: italic;">
                        Data jadwal pengantin tidak ditemukan untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>