<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Jadwal Pengantin</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 0; }
        p { text-align: center; margin-top: 0; font-size: 11px; }
    </style>
</head>
<body>
    <h2>Jadwal Pengantin</h2>
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
                <th>Asisten</th>
                <th>FG</th>
                <th>Layos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $i => $row)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $row->tanggal_display ?? '-' }}</td>
                    <td>{{ $row->bulan ?? '-' }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->alamat }}</td>
                    <td>{{ $row->paket?->nama_paket ?? '-' }}</td>
                    <td>{{ $row->asisten ?? '-' }}</td>
                    <td>{{ $row->fg ?? '-' }}</td>
                    <td>{{ $row->layos ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Tidak ada data jadwal.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
