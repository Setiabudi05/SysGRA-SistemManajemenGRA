<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventory Baju Pengantin</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 12px; margin: 30px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        img { width: 60px; height: 80px; object-fit: cover; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN INVENTORY BAJU PENGANTIN</h2>
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Foto</th>
                <th>Kategori</th>
                <th>Warna</th>
                <th>Size</th>
                <th width="10%">Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td class="text-center">
                    @if($item->foto)
                        <img src="{{ asset('storage/' . $item->foto) }}" alt="foto">
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->kategori }}</td>
                <td>{{ $item->warna }}</td>
                <td class="text-center">{{ $item->ukuran }}</td>
                <td class="text-center">{{ $item->stok }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        // Otomatis memicu dialog print saat halaman dimuat
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>