<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Jadwal Dekorasi</title>
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

        /* Layout KOP Surat Resmi dengan Logo Monogram Teks */
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

        /* Box Informasi Ringkas (Metadata Atas) */
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

        /* Tabel Data Utama - Struktur Fixed Anti-Error */
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

        /* Utility Helpers */
        .fw-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        /* Gaya Khusus Gambar Foto di dalam Tabel */
        .img-cell {
            text-align: center;
            vertical-align: middle;
            padding: 5px !important;
        }

        .img-dekor {
            max-width: 70px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <table class="kop-table">
        <tr>
            <td class="text-container" style="padding-right: 60px;">
                <h2>GRIYA RIAS ASMARA</h2>
                <p>Alamat: Jl. Raya Brebes No. XX, Kabupaten Brebes, Jawa Tengah</p>
                <p>Telp: 08xx-xxxx-xxxx | Email: griyariasasmara@gmail.com</p>
            </td>
        </tr>
    </table>

    <div class="GarisKop"></div>

    <div class="report-title">JADWAL OPERASIONAL DEKORASI</div>

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
                <th style="width: 5%;">NO</th>
                <th style="width: 15%;">TANGGAL</th>
                <th style="width: 14%;">NAMA</th>
                <th style="width: 21%;">ALAMAT LOKASI ACARA</th>
                <th style="width: 15%;">PAKET</th>
                <th style="width: 12%;">FOTO</th>
                <th style="width: 18%;">DESKRIPSI DEKOR</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $i => $row)
                <tr>
                    <td class="text-center fw-bold">{{ $i + 1 }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($row->tanggal_awal)->format('d') }}
                        @if($row->tanggal_akhir && $row->tanggal_akhir != $row->tanggal_awal)
                            - {{ \Carbon\Carbon::parse($row->tanggal_akhir)->format('d') }}
                        @endif
                        {{ $row->bulan }} {{ $row->tahun }}
                    </td>
                    <td class="fw-bold">{{ $row->nama }}</td>
                    <td class="text-left">{{ $row->alamat }}</td>
                    <td class="text-left">{{ $row->paket?->nama_paket ?? '-' }}</td>
                    <td class="img-cell">
                        @if($row->jadwalDekor && $row->jadwalDekor->foto)
                            @php $path = public_path('storage/' . $row->jadwalDekor->foto); @endphp
                            @if(file_exists($path))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($path)) }}" class="img-dekor"
                                    alt="Foto">
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-left">{{ $row->jadwalDekor->deskripsi ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px; color: #6c757d;">
                        Data jadwal dekorasi tidak ditemukan untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>