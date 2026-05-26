<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Jadwal Layos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h3, h4 {
            margin: 0;
            text-align: center;
        }
        .header {
            margin-bottom: 15px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #444;
        }
        th, td {
            padding: 6px 8px;
            text-align: center;
        }
        th {
            background: #f2f2f2;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>JADWAL LAYOS</h3>
        @if($bulan && $tahun)
            <h4> Bulan {{ $bulan }} {{ $tahun }}</h4>
        @elseif($bulan)
            <h4>{{ $bulan }}</h4>
        @elseif($tahun)
            <h4>{{ $tahun }}</h4>
        @else
            <h4>Semua Periode</h4>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Nama Pengantin</th>
                <th>Paket</th>
                <th>Layos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $index => $j)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if($j->pengantin)
                            @php
                                $tglAwal = \Carbon\Carbon::parse($j->pengantin->tanggal_awal)->translatedFormat('d');
                                $tglAkhir = $j->pengantin->tanggal_akhir
                                    ? \Carbon\Carbon::parse($j->pengantin->tanggal_akhir)->translatedFormat('d')
                                    : null;
                            @endphp
                            {{ $tglAkhir && $tglAkhir != $tglAwal ? $tglAwal . ' - ' . $tglAkhir : $tglAwal }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $j->bulan }}</td>
                    <td>{{ $j->tahun }}</td>
                    <td class="text-left">{{ $j->pengantin->nama ?? '-' }}</td>
                    <td>{{ $j->pengantin->paket->nama_paket ?? '-' }}</td>
                    <td>{{ $j->layos }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
