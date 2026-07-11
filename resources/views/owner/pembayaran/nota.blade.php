<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Transaksi #PAY-{{ $transaksi->id }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 13px; color: #333; }
        .container { width: 100%; max-width: 700px; margin: auto; padding: 20px; border: 1px solid #ccc; }
        .header { text-align: center; border-bottom: 2px double #333; margin-bottom: 20px; padding-bottom: 10px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #333; padding: 10px; }
        .total-row { font-weight: bold; font-size: 1.1em; background-color: #f8f9fa; }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="btn-print" style="text-align: right; margin-bottom: 20px;">
            <button onclick="window.print()">Cetak Nota</button>
        </div>
        
        <div class="header">
            <h2>GRIYA RIAS ASMARA</h2>
            <p>Dusun Karanganyar, RT.02/RW.01 | WA: 085874837593</p>
        </div>

        <table class="info-table">
            <tr>
                <td><strong>No. Transaksi</strong></td><td>: #PAY-{{ $transaksi->id }}</td>
                <td><strong>Tgl Bayar</strong></td><td>: {{ $transaksi->created_at->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td><strong>Klien</strong></td><td>: {{ $booking->customer_name ?? '-' }}</td>
                <td><strong>Paket</strong></td><td>: {{ $booking->paket->nama_paket ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Pengantin</strong></td><td>: {{ $booking->bride_groom_name ?? '-' }}</td>
                <td><strong>Tgl Acara</strong></td><td>: {{ isset($booking->event_date) ? date('d M Y', strtotime($booking->event_date)) : '-' }}</td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Deskripsi Pembayaran</th>
                    <th style="text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Pembayaran cicilan untuk pengantin: {{ $booking->bride_groom_name }}</td>
                    <td style="text-align: right;">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td>TOTAL DIBAYARKAN PADA TRANSAKSI INI</td>
                    <td style="text-align: right;">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%; margin-top: 50px;">
            <tr>
                <td style="text-align: center;">Hormat Kami,<br><br><br><br>( Admin GRA )</td>
                <td style="text-align: center;">Penerima,<br><br><br><br>( {{ $booking->customer_name ?? 'Klien' }} )</td>
            </tr>
        </table>
    </div>
</body>
</html>