<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembukuan SysGRA</title>
    <style>
        /* Pengaturan orientasi Portrait dan margin kertas */
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; 
            font-size: 13px; 
            color: #333; 
            line-height: 1.4;
        }

        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 5px 0; font-size: 14px; }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 25px; 
        }

        th, td { 
            border: 1px solid #aaa; 
            padding: 8px; 
            text-align: left; 
        }

        th { background-color: #f2f2f2; font-weight: bold; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        
        .section-title { 
            background: #eee; 
            padding: 5px 10px; 
            font-size: 14px; 
            font-weight: bold; 
            margin-bottom: 10px; 
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>Laporan Pembukuan SysGRA</h1>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($start)->format('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($end)->format('d M Y') }}</strong></p>
    </div>

    <div class="section-title">Pemasukan</div>
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th>Customer</th>
                <th width="25%" class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataPemasukan as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->customer ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-right">Total Pemasukan</td>
                <td class="text-right">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Pengeluaran</div>
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th>Keterangan</th>
                <th width="25%" class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataPengeluaran as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-right">Total Pengeluaran</td>
                <td class="text-right">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: right; font-size: 16px;">
        <p><strong>Saldo Bersih:</strong> 
            <span style="color: {{ ($totalMasuk - $totalKeluar) >= 0 ? '#198754' : '#dc3545' }}">
                Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}
            </span>
        </p>
    </div>

</body>
</html>