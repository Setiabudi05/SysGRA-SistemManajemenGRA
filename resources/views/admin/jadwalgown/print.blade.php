<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Jadwal Gown</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        p {
            text-align: center;
            margin-top: 0;
            font-size: 11px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #3498db;
            color: #fff;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #888;
        }
    </style>
</head>

<body>
    <h2>Jadwal Gown</h2>
    <p>
        @if(!empty($bulan)) Bulan: {{ $bulan }} @endif
        @if(!empty($tahun)) Tahun: {{ $tahun }} @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Bulan</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Paket</th>
                <th>Gown</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($row->jadwalPengantin?->tanggal_awal && $row->jadwalPengantin?->tanggal_akhir)
                            {{ \Carbon\Carbon::parse($row->jadwalPengantin->tanggal_awal)->format('d') }} -
                            {{ \Carbon\Carbon::parse($row->jadwalPengantin->tanggal_akhir)->format('d') }}
                        @elseif($row->jadwalPengantin?->tanggal_awal)
                            {{ \Carbon\Carbon::parse($row->jadwalPengantin->tanggal_awal)->format('d') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $row->bulan ?? '-' }}</td>
                    <td>{{ $row->jadwalPengantin->nama ?? '-' }}</td>
                    <td>{{ $row->jadwalPengantin->alamat ?? '-' }}</td>
                    <td>{{ $row->jadwalPengantin?->paket?->nama_paket ?? '-' }}</td>
                    <td>{{ $row->gown ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data jadwal.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>