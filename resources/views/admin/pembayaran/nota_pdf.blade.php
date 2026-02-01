<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nota Pembayaran #{{ $pembayaran->id }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #333; }
        .container { width: 100%; max-width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; }
        .header { text-align: center; border-bottom: 2px double #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .data-table th { background-color: #f8f9fa; }
        .summary { margin-top: 20px; }
        .summary-table { float: right; width: 350px; border-collapse: collapse; }
        .summary-table td { padding: 5px; }
        .footer-sign { width: 100%; margin-top: 50px; }
        .footer-sign td { text-align: center; width: 50%; }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="btn-print" style="text-align: right; margin-bottom: 10px;">
            <button onclick="window.print()">Cetak Nota</button>
        </div>

        <div class="header">
            <h2>GRIYA RIAS ASMARA (GRA)</h2>
            <p>Alamat: Dusun Karanganyar, RT.02/RW.01 | WA: 085874837593</p>
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 15%;">No. Nota</td><td>: #PAY-{{ $pembayaran->id }}</td>
                <td style="width: 20%;">Tanggal Cetak</td><td>: {{ date('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Klien</td><td>: {{ $booking->customer_name ?? '-' }}</td>
                <td>Pengantin</td><td>: {{ $booking->bride_groom_name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tgl Acara</td><td>: {{ $booking->wedding_date ? date('d M Y', strtotime($booking->wedding_date)) : '-' }}</td>
                <td>Paket</td><td>: {{ $booking->package_name ?? '-' }}</td>
            </tr>
        </table>

        <h4>RIWAYAT PEMBAYARAN VALID:</h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tgl Bayar</th>
                    <th>Keterangan</th>
                    <th style="text-align: right;">Jumlah (Nominal)</th>
                </tr>
            </thead>
            <tbody>
                @php $totalValid = 0; @endphp
                @foreach($booking->pembayarans->where('status_pembayaran', 'valid') as $history)
                <tr>
                    <td>{{ $history->created_at->format('d/m/Y') }}</td>
                    <td>{{ $loop->first ? 'DP (Down Payment)' : 'Angsuran/Pelunasan' }}</td>
                    <td style="text-align: right;">Rp {{ number_format($history->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
                @php $totalValid += $history->jumlah_bayar; @endphp
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            @php
                // Membersihkan package_price dari titik/teks agar bisa dihitung
                $priceRaw = $booking->package_price ?? 0;
                $cleanPrice = is_numeric($priceRaw) ? $priceRaw : (int) preg_replace('/[^0-9]/', '', $priceRaw);
                $sisa = $cleanPrice - $totalValid;
            @endphp
            <table class="summary-table">
                <tr>
                    <td>Total Harga Paket</td>
                    <td style="text-align: right;">: Rp {{ number_format($cleanPrice, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Sudah Dibayar</td>
                    <td style="text-align: right;">: Rp {{ number_format($totalValid, 0, ',', '.') }}</td>
                </tr>
                <tr style="font-weight: bold; border-top: 1px solid #333;">
                    <td>SISA TAGIHAN</td>
                    <td style="text-align: right; color: red;">: Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                </tr>
            </table>
            <div style="clear: both;"></div>
        </div>

        <table class="footer-sign">
            <tr>
                <td>
                    <p>Hormat Kami,</p>
                    <br><br><br>
                    <p><strong>( Admin GRA )</strong></p>
                </td>
                <td>
                    <p>Penerima,</p>
                    <br><br><br>
                    <p><strong>( {{ $booking->customer_name ?? 'Klien' }} )</strong></p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>