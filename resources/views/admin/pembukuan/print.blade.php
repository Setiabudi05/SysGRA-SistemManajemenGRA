<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembukuan - {{ $tanggal }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2, h3 { text-align: center; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-top: 20px; }
    </style>
</head>
<body>
    <h2>Laporan Pembukuan</h2>
    <h3>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</h3>

    {{-- Tabel Pemasukan --}}
    <h4>Pemasukan</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th>Keterangan</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pemasukan as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->customer ?? '-' }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data pemasukan</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total Pemasukan</th>
                <th class="text-right">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    {{-- Tabel Pengeluaran --}}
    <h4>Pengeluaran</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Keterangan</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengeluaran as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Tidak ada data pengeluaran</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total Pengeluaran</th>
                <th class="text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    {{-- Ringkasan Saldo --}}
    <div class="summary">
        <table>
            <tr>
                <th>Total Pemasukan</th>
                <td class="text-right">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total Pengeluaran</th>
                <td class="text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Saldo Akhir</th>
                <td class="text-right"><strong>Rp {{ number_format($saldo, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <br><br>
    <div style="text-align: right; margin-top: 50px;">
        <p>Brebes, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <br><br><br>
        <p>_____________________________</p>
        <p style="margin-top:-10px;">Tanda Tangan</p>
    </div>
</body>
</html>
