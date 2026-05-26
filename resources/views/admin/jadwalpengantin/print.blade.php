<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Jadwal Pengantin</title>
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

        /* Layout KOP Surat Resmi dengan Logo (Tabel Tanpa Border) */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .kop-table td {
            border: none !important;
            padding: 0 !important;
        }

        /* Mengunci posisi logo imitasi di sebelah atas kiri */
        .logo-container {
            width: 80px;
            vertical-align: middle;
        }

        /* TRIK SAKTI: Membentuk logo inisial "G" (Griya) mewah menggunakan susunan kotak HTML murni */
        .pixel-logo {
            border-collapse: collapse;
            display: inline-block;
        }
        .pixel-logo td {
            width: 10px !important;
            height: 10px !important;
            padding: 0 !important;
        }
        .bg-dark { background-color: #2c3e50 !important; } /* Warna logo emas/abu gelap WO */
        .bg-gold { background-color: #d4af37 !important; }

        .text-container {
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
            text-align: center;
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

        /* Box Informasi Ringkas (Metadata Atas) */
        .info-box {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-box td {
            border: none !important;
            padding: 5px 0 !important;
            vertical-align: top;
            font-size: 10pt;
        }

        /* Tabel Data Utama */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table.data-table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6 !important;
            padding: 12px 8px !important;
            font-size: 9pt;
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
        }

        table.data-table td {
            border: 1px solid #dee2e6 !important;
            padding: 10px 8px !important;
            font-size: 9pt;
            vertical-align: middle;
        }

        /* Helper Utility Classes */
        .fw-bold { font-weight: bold; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }

        /* Batasan Lebar Kolom Proporsional */
        .col-no { width: 5%; }
        .col-tgl { width: 14%; }
        .col-nama { width: 15%; }
        .col-alamat { width: 21%; }
        .col-paket { width: 15%; }
        .col-kru { width: 10%; }
    </style>
</head>

<body>

    <table class="kop-table">
        <tr>
            <td class="logo-container">
                <table class="pixel-logo">
                    <tr>
                        <td class="bg-gold"></td><td class="bg-gold"></td><td class="bg-gold"></td><td class="bg-gold"></td><td class="bg-gold"></td>
                    </tr>
                    <tr>
                        <td class="bg-gold"></td><td></td><td></td><td></td><td></td>
                    </tr>
                    <tr>
                        <td class="bg-gold"></td><td></td><td class="bg-dark"></td><td class="bg-dark"></td><td class="bg-dark"></td>
                    </tr>
                    <tr>
                        <td class="bg-gold"></td><td></td><td></td><td></td><td class="bg-dark"></td>
                    </tr>
                    <tr>
                        <td class="bg-gold"></td><td class="bg-gold"></td><td class="bg-gold"></td><td class="bg-gold"></td><td class="bg-dark"></td>
                    </tr>
                </table>
            </td>
            <td class="text-container" style="padding-right: 80px !important;">
                <h2>GRIYA RIAS ASMARA</h2>
                <p>Alamat: Jl. Raya Brebes No. XX, Kabupaten Brebes, Jawa Tengah</p>
                <p>Telp: 08xx-xxxx-xxxx | Email: griyariasasmara@gmail.com</p>
            </td>
        </tr>
    </table>

    <div class="report-title">JADWAL OPERASIONAL PENGANTIN</div>

    <table class="info-box">
        <tr>
            <td style="width: 18%;">Periode Jadwal</td>
            <td style="width: 2%;">:</td>
            <td style="width: 30%;" class="fw-bold">{{ $bulan ?? 'Semua' }} {{ $tahun ?? '' }}</td>
            
            <td style="width: 18%;">Tanggal Cetak</td>
            <td style="width: 2%;">:</td>
            <td style="width: 30%;">{{ date('d F Y') }}</td>
        </tr>
        <tr>
            <td>Jumlah Jadwal</td>
            <td>:</td>
            <td class="fw-bold">{{ $jadwal->count() }} Acara</td>
            
            <td>Cetak Oleh</td>
            <td>:</td>
            <td class="fw-bold" style="text-transform: uppercase;">Admin (SysGRA)</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-tgl">TANGGAL</th>
                <th class="col-nama">NAMA PENGANTIN</th>
                <th class="col-alamat">ALAMAT LOKASI ACARA</th>
                <th class="col-paket">PAKET LAYANAN</th>
                <th class="col-kru">ASISTEN</th>
                <th class="col-kru">FG</th>
                <th class="col-kru">LAYOS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $i => $row)
                <tr>
                    <td class="text-center fw-bold">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $row->tanggal_display ?? '-' }} {{ $row->bulan ?? '' }} {{ $row->tahun ?? '' }}</td>
                    <td class="fw-bold">{{ $row->nama }}</td>
                    <td class="text-left">{{ $row->alamat }}</td>
                    <td class="text-left">{{ $row->paket?->nama_paket ?? '-' }}</td>
                    <td class="text-center">{{ $row->asisten ?? '-' }}</td>
                    <td class="text-center">{{ $row->fg ?? '-' }}</td>
                    <td class="text-center">{{ $row->layos ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 30px; color: #6c757d;">
                        Data jadwal pengantin tidak ditemukan untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>