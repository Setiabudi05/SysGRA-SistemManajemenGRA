<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Jadwal Dekorasi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        th {
            background: #f2f2f2;
        }

        h2 {
            text-align: center;
            margin-bottom: 0;
        }

        p {
            text-align: center;
            margin-top: 0;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <h2>Jadwal Dekorasi</h2>
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
                <th>Foto</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($row->jadwalPengantin)
                            {{ \Carbon\Carbon::parse($row->jadwalPengantin->tanggal_awal)->format('d') }}
                            @if($row->jadwalPengantin->tanggal_akhir && $row->jadwalPengantin->tanggal_akhir != $row->jadwalPengantin->tanggal_awal)
                                - {{ \Carbon\Carbon::parse($row->jadwalPengantin->tanggal_akhir)->format('d') }}
                            @endif
                            {{ $row->bulan }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $row->bulan }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->alamat }}</td>
                    <td>{{ $row->paket?->nama_paket ?? '-' }}</td>
                    <td>
                        @if($row->foto)
                            <img src="{{ public_path('storage/' . $row->foto) }}" width="80">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $row->deskripsi ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data jadwal dekorasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>