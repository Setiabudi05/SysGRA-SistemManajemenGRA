<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jadwal Operasional - {{ $bulan ?? 'Semua' }} {{ $tahun ?? '' }}</title>
    <style>
        @page { margin: 1.2cm; size: A4 portrait; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9pt; color: #333; line-height: 1.4; }
        
        /* Watermark Premium */
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80pt; color: rgba(212, 175, 55, 0.08); z-index: -1; pointer-events: none; }

        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .kop-table td { border: none !important; padding: 0 !important; vertical-align: middle; }
        .kop-logo-side { width: 70px; }
        .kop-text-side { text-align: center; }
        
        .kop-text-side h2 { margin: 0; font-size: 17pt; letter-spacing: 2px; color: #000; text-transform: uppercase; font-weight: bold; }
        .kop-text-side p { margin: 3px 0; font-size: 8.5pt; color: #555; }

        .double-line { border-top: 2px solid #2c3e50; border-bottom: 0.5px solid #2c3e50; height: 3px; margin: 10px 0 20px 0; }
        
        .report-title { text-align: center; font-weight: bold; font-size: 11pt; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        table.data-table th { background-color: #2c3e50 !important; color: #d4af37 !important; border: 1px solid #2c3e50 !important; padding: 8px 4px !important; font-size: 8pt; text-align: center; text-transform: uppercase; }
        table.data-table td { border: 1px solid #cccccc !important; padding: 6px 4px !important; font-size: 8pt; word-wrap: break-word; }
    </style>
</head>
<body>
    <div class="watermark">GRIYA RIAS ASMARA</div>

    <table class="kop-table">
        <tr>
            <td class="kop-logo-side"><div style="width: 50px; height: 50px; background: #2c3e50;"></div></td>
            <td class="kop-text-side">
                <h2>GRIYA RIAS ASMARA</h2>
                <p>Alamat: Gg.Bons Royen 4 Dukuh kendaga Rt 02/11 Kec Larangan, Kab. Brebes</p>
                <p>Telp: 085866659930 | Email: griyariasasmara@gmail.com</p>
            </td>
            <td class="kop-logo-side"></td>
        </tr>
    </table>
    <div class="double-line"></div>

    <div class="report-title">LAPORAN JADWAL OPERASIONAL (OWNER VIEW)</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 15%;">TANGGAL</th>
                <th style="width: 15%;">PENGANTIN</th>
                <th style="width: 25%;">ALAMAT</th>
                <th style="width: 15%;">PAKET</th>
                <th style="width: 8%;">ASN</th>
                <th style="width: 8%;">FG</th>
                <th style="width: 9%;">LYS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $row)
            <tr>
                <td align="center">{{ $i + 1 }}</td>
                <td align="center">
                    {{ \Carbon\Carbon::parse($row->tanggal_awal)->translatedFormat('d F Y') }}
                </td>
                <td style="font-weight: bold;">{{ $row->nama }}</td>
                <td>{{ $row->alamat }}</td>
                <td>{{ $row->paket->nama_paket ?? '-' }}</td>
                <td align="center">{{ $row->asisten ?? '-' }}</td>
                <td align="center">{{ $row->fg ?? '-' }}</td>
                <td align="center">{{ $row->layos ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" align="center">Data tidak ditemukan untuk periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 60%; font-size: 8pt; color: #555;">
                * Laporan ini dihasilkan secara sistematis oleh SysGRA.
            </td>
            <td style="text-align: center; font-size: 8pt;">
                <div style="margin-bottom: 50px;">Brebes, {{ date('d F Y') }}</div>
                <div style="font-weight: bold; text-decoration: underline;">Afip Listiyana</div>
                <div>Owner Griya Rias Asmara</div>
            </td>
        </tr>
    </table>
</body>
</html>