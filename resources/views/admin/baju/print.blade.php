<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Eksklusif - Griya Rias Asmara</title>
    <style>
        @page { margin: 1cm; } /* Margin diperkecil agar ruang lebih lega */
        * { box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #333; line-height: 1.4; margin: 0; padding: 0; }

        /* COVER PAGE */
        .cover-page { width: 100%; text-align: center; padding-top: 4cm; page-break-after: always; }
        .cover-accent-line { width: 80px; height: 4px; background-color: #d4af37; margin: 20px auto; border-radius: 2px; }
        .cover-title { font-size: 26pt; font-weight: bold; color: #1a202c; letter-spacing: 3px; text-transform: uppercase; }
        .cover-subtitle { font-size: 14pt; color: #d4af37; letter-spacing: 2px; text-transform: uppercase; font-weight: 600; margin-bottom: 30px; }
        .cover-brand { font-size: 18pt; font-weight: bold; color: #000; letter-spacing: 1px; text-transform: uppercase; }
        .cover-footer { margin-top: 5cm; font-size: 9pt; color: #718096; line-height: 1.6; }

        /* HEADER KOP */
        table.kop-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.kop-table td { border: none !important; padding: 0 !important; }
        .text-container { width: 100%; text-align: center; }
        .text-container h2 { margin: 0; font-size: 18pt; letter-spacing: 2px; text-transform: uppercase; color: #000; }
        .text-container p { margin: 2px 0; font-size: 8.5pt; color: #4a5568; }
        .GarisKop { border-bottom: 3px double #333; margin-bottom: 10px; width: 100%; }
        .report-title { text-align: center; font-weight: bold; font-size: 13pt; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
        
        /* GRID LAYOUT - 2 KOLOM */
        .paket-wrapper { page-break-inside: avoid; margin-bottom: 15px; }
        .package-header { background-color: #f1f3f5; padding: 6px 10px; font-size: 10pt; font-weight: bold; margin-bottom: 10px; border-left: 5px solid #d4af37; text-transform: uppercase; }
        
        table.grid-table { width: 100%; border-collapse: separate; border-spacing: 10px; }
        table.grid-table td { width: 50%; vertical-align: top; border: 1px solid #e2e8f0 !important; border-radius: 8px; padding: 10px !important; background-color: #fff; page-break-inside: avoid; }
        
        /* FOTO SANGAT BESAR */
        .img-frame { 
            width: 100%; 
            height: 350px; 
            text-align: center; 
            background-color: #fcfcfc; 
            border-bottom: 1px solid #edf2f7; 
            margin-bottom: 10px; 
            overflow: hidden; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        .img-frame img { max-width: 100%; max-height: 100%; object-fit: contain; }
        
        .desc-text { font-size: 10pt; color: #2d3748; text-align: left; }
    </style>
</head>
<body>

    <div class="cover-page">
        <div class="cover-brand">Griya Rias Asmara</div>
        <div class="cover-accent-line"></div>
        <div class="cover-title">Katalog Visual</div>
        <div class="cover-subtitle">Koleksi Baju Pengantin Eksklusif</div>
        <div class="cover-footer">
            <strong>GANG. BONS ROYEN 4 DUKUH KENDAGA</strong><br>
            Larangan, Kec. Larangan, Kabupaten Brebes, Jawa Tengah 52262<br>
            <span style="color: #d4af37; font-weight: bold;">Telp/WA: +62 838-6130-0111</span>
        </div>
    </div>

    <table class="kop-table">
        <tr>
            <td class="text-container">
                <h2>GRIYA RIAS ASMARA</h2>
                <p>Gang. Bons Royen 4 Dukuh Kendaga, Larangan, Kec. Larangan</p>
                <p>Kabupaten Brebes, Jawa Tengah 52262</p>
                <p>Telp/WA: +62 838-6130-0111</p>
            </td>
        </tr>
    </table>
    <div class="GarisKop"></div>
    <div class="report-title">KATALOG VISUAL PILIHAN BAJU PENGANTIN</div>

    @foreach($dataPaket as $namaPaket => $daftarBaju)
        <div class="paket-wrapper">
            <div class="package-header">PAKET WEDDING: {{ $namaPaket }}</div>
            <table class="grid-table">
                @php $count = 0; @endphp
                @foreach($daftarBaju as $row)
                    @if($count % 2 == 0) <tr> @endif
                    <td>
                        <div class="img-frame">
                            @php $path = public_path('storage/' . $row->foto_gown); @endphp
                            @if($row->foto_gown && file_exists($path))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($path)) }}" alt="Gaun">
                            @else
                                <div style="color: #94a3b8;">No Image</div>
                            @endif
                        </div>
                        <div class="desc-text">
                            <strong style="color: #1a202c; font-size: 11pt;">{{ $row->nama_gown }}</strong><br>
                            {{ $row->deskripsi_gown ?? '-' }}
                        </div>
                    </td>
                    @php $count++; @endphp
                    @if($count % 2 == 0) </tr> @endif
                @endforeach
                @if($count % 2 != 0)
                    <td style="border: none !important; background: transparent;"></td>
                    </tr>
                @endif
            </table>
        </div>
    @endforeach

</body>
</html>