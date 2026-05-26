<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Pekerjaan - {{ $namaKru }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }

        /* Layout KOP Surat Resmi dengan Logo Monogram Teks (Tanpa Border Liar) */
        table.kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table.kop-table td {
            border: none !important;
            padding: 0 !important;
        }

        .logo-container {
            width: 15%;
            vertical-align: middle;
        }

        .monogram-logo {
            font-size: 28pt;
            font-weight: bold;
            color: #d4af37;
            line-height: 1;
        }

        .text-container {
            width: 85%;
            text-align: center;
            vertical-align: middle;
        }

        .text-container h2 {
            margin: 0;
            font-size: 18pt;
            letter-spacing: 2px;
            color: #000;
            text-transform: uppercase;
        }

        .text-container p {
            margin: 3px 0;
            font-size: 9pt;
            color: #555;
        }

        .GarisKop {
            border-bottom: 3px double #333;
            margin-bottom: 20px;
            width: 100%;
        }

        /* Judul Laporan */
        .report-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Box Informasi Ringkas Metadata */
        table.info-box {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        table.info-box td {
            border: none !important;
            padding: 4px 0 !important;
            vertical-align: top;
            font-size: 10pt;
        }

        /* Tabel Data Utama - Struktur Fixed Anti-Error Row */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: fixed;
        }

        table.data-table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6 !important;
            padding: 10px 5px !important;
            font-size: 9pt;
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
        }

        table.data-table td {
            border: 1px solid #dee2e6 !important;
            padding: 8px 5px !important;
            font-size: 9pt;
            vertical-align: middle;
            word-wrap: break-word;
        }

        /* Penekanan Teks */
        .fw-bold { font-weight: bold; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .status-ok {
            color: #198754;
            font-weight: bold;
        }

        /* Footer Tanda Tangan */
        table.footer-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }

        table.footer-table td {
            border: none !important;
            padding: 0 !important;
        }

        .signature-box {
            text-align: center;
            width: 40%;
            font-size: 10pt;
        }

        .spacer { 
            height: 55px; 
        }
    </style>
</head>

<body>

    <table class="kop-table">
        <tr>
            <td class="logo-container">
                <div class="monogram-logo">G</div>
            </td>
            <td class="text-container" style="padding-right: 60px;">
                <h2>GRIYA RIAS ASMARA</h2>
                <p>Alamat: Jl. Raya Brebes No. XX, Kabupaten Brebes, Jawa Tengah</p>
                <p>Telp: 08xx-xxxx-xxxx | Email: griyariasasmara@gmail.com</p>
            </td>
        </tr>
    </table>
    
    <div class="GarisKop"></div>

    <div class="report-title">LAPORAN RIWAYAT PENUGASAN KRU</div>

    <table class="info-box">
        <tr>
            <td style="width: 15%;">Nama Kru</td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;" class="fw-bold">{{ $namaKru }}</td>
            
            <td style="width: 15%;">Periode</td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;" class="fw-bold">{{ $bulan }} {{ $tahun }}</td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td>:</td>
            <td>{{ date('d F Y') }}</td>
            
            <td>Status Data</td>
            <td>:</td>
            <td class="status-ok" style="font-size: 9pt;">SELESAI (PERMANEN)</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 16%;">Tanggal</th>
                <th style="width: 18%;">Pengantin</th>
                <th style="width: 24%;">Lokasi Acara</th>
                <th style="width: 22%;">Paket Layanan</th>
                <th style="width: 15%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $index => $item)
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->tanggal_display }}</td>
                    <td class="fw-bold">{{ $item->nama }}</td>
                    <td class="text-left">{{ $item->alamat }}</td>
                    <td class="text-left">{{ $item->paket?->nama_paket ?? '-' }}</td>
                    <td class="text-center status-ok" style="font-size: 8.5pt;">SELESAI</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 30px; color: #6c757d;">
                        Data tidak ditemukan untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 60%;"></td>
            <td class="signature-box">
                <p>Brebes, {{ date('d F Y') }}</p>
                <p style="margin-top: -10px;">Petugas Lapangan,</p>
                <div class="spacer"></div>
                <p class="fw-bold" style="text-decoration: underline;">( {{ $namaKru }} )</p>
                <p style="font-size: 8.5pt; margin-top: -10px; color: #555;">ID Kru: {{ Auth::user()->id }}</p>
            </td>
        </tr>
    </table>

</body>

</html>