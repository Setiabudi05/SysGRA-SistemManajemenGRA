<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Owner - Cetak Jadwal Pengantin</title>
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

        /* Kop Surat Mewah Minimalis Monogram */
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

        .report-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Metadata Laporan */
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

        /* Tabel Data Utama */
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

        .fw-bold { font-weight: bold; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
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

    <div class="report-title">LAPORAN JADWAL & PENUGASAN KRU</div>

    <table class="info-box">
        <tr>
            <td style="width: 18%;">Periode Laporan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 30%;" class="fw-bold">{{ $bulan ?? 'Semua' }} {{ $tahun ?? '' }}</td>
            
            <td style="width: 18%;">Tanggal Cetak</td>
            <td style="width: 2%;">:</td>
            <td style="width: 30%;">{{ date('d F Y') }}</td>
        </tr>
        <tr>
            <td>Total Agenda</td>
            <td>:</td>
            <td class="fw-bold">{{ $jadwal->count() }} Acara</td>
            
            <td>Dicetak Oleh</td>
            <td>:</td>
            <td class="fw-bold" style="text-transform: uppercase;">Owner (SysGRA)</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 13%;">TANGGAL</th>
                <th style="width: 15%;">PENGANTIN</th>
                <th style="width: 19%;">LOKASI ACARA</th>
                <th style="width: 16%;">PAKET</th>
                <th style="width: 12%;">ASISTEN</th>
                <th style="width: 10%;">FG</th>
                <th style="width: 10%;">LAYOS</th>
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
                    <td class="text-center" style="font-size: 8.5pt;">{{ $row->asisten ?? 'Belum diplot' }}</td>
                    <td class="text-center" style="font-size: 8.5pt;">{{ $row->fg ?? 'Belum diplot' }}</td>
                    <td class="text-center" style="font-size: 8.5pt;">{{ $row->layos ?? 'Belum diplot' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 30px; color: #6c757d;">
                        Tidak ada agenda jadwal pengantin pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>