<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Jadwal Penugasan - {{ Auth::user()->name }}</title>
    <style>
        @page {
            margin: 0.8cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.5;
        }

        /* Header Style */
        .header {
            text-align: center;
            border-bottom: 2px solid #435ebe;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            text-transform: uppercase;
            color: #435ebe;
            margin: 0;
            font-size: 18px;
            letter-spacing: 1px;
        }

        .header p {
            margin: 4px 0 0;
            color: #555;
            font-size: 12px;
            font-weight: bold;
        }

        /* Table Style - Dibuat Mirip DataTables Dashboard */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
            /* Menjaga kolom tidak meluber */
        }

        th {
            background-color: #f8f9fa;
            color: #444;
            text-transform: uppercase;
            padding: 12px 5px;
            font-size: 9px;
            border: 1px solid #dee2e6;
            text-align: center;
        }

        td {
            border: 1px solid #dee2e6;
            padding: 10px 6px;
            vertical-align: top;
            word-wrap: break-word;
            /* Alamat panjang akan terpotong ke bawah */
        }

        tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* Helper Classes */
        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-primary {
            color: #435ebe;
        }

        /* Column Sizing */
        .col-no {
            width: 25px;
        }

        .col-tgl {
            width: 85px;
        }

        .col-nama {
            width: 100px;
        }

        .col-alamat {
            width: 140px;
        }

        .col-paket {
            width: 80px;
        }

        .col-tim {
            width: auto;
        }

        /* Meta Info */
        .meta-info {
            margin-bottom: 10px;
            width: 100%;
        }

        .meta-info td {
            border: none;
            padding: 0;
            font-size: 11px;
            background: none !important;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Jadwal Penugasan Kru</h2>
        <p>Griya Rias Asmara (SysGRA)</p>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 80px;">Nama Kru</td>
            <td>: <strong>{{ Auth::user()->name }}</strong></td>
            <td style="text-align: right; color: #666;">
                Periode: <strong>{{ $bulan }} {{ $tahun }}</strong>
            </td>
        </tr>
        <tr>
            <td>Dicetak pada</td>
            <td>: {{ date('d/m/Y H:i') }}</td>
            <td style="text-align: right; font-style: italic; color: #888;">Dokumen Operasional Internal</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-tgl">Tanggal</th>
                <th class="col-nama">Nama</th>
                <th class="col-alamat">Alamat</th>
                <th class="col-paket">Paket</th>
                <th class="col-tim">Asisten</th>
                <th class="col-tim">FG</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center fw-bold">
                        {{ $row->tanggal_display }} <!-- Sudah diformat di controller -->
                    </td>
                    <td class="fw-bold text-primary">{{ $row->nama }}</td>
                    <td style="font-size: 9px;">{{ $row->alamat }}</td>
                    <td class="text-center">{{ $row->paket?->nama_paket ?? '-' }}</td>
                    <td style="font-size: 9px;">{{ $row->asisten ?? '-' }}</td>
                    <td class="text-center fw-bold">{{ $row->fg ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px;">
                        Tidak ada data jadwal untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div
        style="margin-top: 30px; border-top: 1px dashed #ccc; padding-top: 10px; text-align: center; font-size: 9px; color: #777;">
        * Harap pastikan peralatan sudah siap dan sampai di lokasi tepat waktu sesuai arahan koordinator.
    </div>
</body>

</html>