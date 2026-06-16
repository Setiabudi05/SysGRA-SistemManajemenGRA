<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Eksklusif Koleksi Baju - Griya Rias Asmara</title>
    <style>
        /* Mengatur margin dasar halaman PDF */
        @page { 
            margin: 1.2cm; 
        }
        
        /* Reset dasar agar hitungan tinggi elemen presisi */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* STYLE HALAMAN SAMPUL (COVER) - SINKRON MODUL DEKORASI */
        .cover-page {
            width: 100%;
            height: 100%;
            text-align: center;
            padding-top: 4cm; 
            page-break-after: always; /* Memaksa konten utama mulai di halaman 2 */
        }
        .cover-accent-line {
            width: 80px;
            height: 4px;
            background-color: #d4af37; /* Warna emas premium GRA */
            margin: 20px auto;
            border-radius: 2px;
        }
        .cover-title {
            font-size: 26pt;
            font-weight: bold;
            color: #1a202c;
            letter-spacing: 3px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .cover-subtitle {
            font-size: 14pt;
            color: #d4af37;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 30px;
        }
        .cover-brand {
            font-size: 18pt;
            font-weight: bold;
            color: #000;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .cover-footer {
            margin-top: 5cm; 
            font-size: 9pt;
            color: #718096;
            line-height: 1.6;
        }

        /* Layout KOP Surat Resmi GRA (Mulai Halaman 2) */
        table.kop-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
        }
        table.kop-table td { 
            border: none !important; 
            padding: 0 !important; 
        }
        .text-container { 
            width: 100%; 
            text-align: center; 
        }
        .text-container h2 { 
            margin: 0; 
            font-size: 18pt; 
            letter-spacing: 2px; 
            text-transform: uppercase; 
            color: #000;
        }
        .text-container p { 
            margin: 4px 0; 
            font-size: 8.5pt; 
            color: #4a5568; 
        }
        .GarisKop { 
            border-bottom: 3px double #333; 
            margin-bottom: 20px; 
            width: 100%; 
        }
        .report-title { 
            text-align: center; 
            font-weight: bold; 
            font-size: 13pt; 
            margin-bottom: 15px; 
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
        
        /* Layout Pembungkus Paket per Lembar */
        .paket-wrapper {
            page-break-inside: avoid;
        }
        /* Memaksa paket kedua dan seterusnya otomatis pindah lembar baru */
        .paket-wrapper-pagebreak {
            page-break-before: always;
            page-break-inside: avoid;
        }

        /* Header Pembatas Kategori Paket Wedding */
        .package-header {
            background-color: #f1f3f5;
            padding: 8px 12px;
            font-size: 11pt;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 15px;
            border-left: 5px solid #d4af37; 
            text-transform: uppercase;
            color: #1a202c;
        }
        
        /* Layout Grid Kartu Menggunakan Tabel Murni (Anti Gagal DomPDF) */
        table.grid-table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 15px; 
            margin-bottom: 10px;
        }
        table.grid-table td {
            width: 33.33%;
            vertical-align: top;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px;
            padding: 10px !important;
            background-color: #fff;
            page-break-inside: avoid; 
        }
        .img-frame {
            width: 100%;
            height: 180px;
            text-align: center;
            background-color: #f8f9fa;
            border-bottom: 1px solid #edf2f7;
            margin-bottom: 10px;
            overflow: hidden;
        }
        .img-frame img { 
            max-width: 100%; 
            max-height: 100%; 
        }
        .desc-text { 
            font-size: 8.5pt; 
            color: #4a5568; 
            margin-top: 5px; 
            text-align: left;
            line-height: 1.4;
        }
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
                <p>Kabupaten Brebes, Jawa Tengah 52262, Indonesia</p>
                <p class="fw-bold" style="margin-top: 5px;">Telp/WA: +62 838-6130-0111 | Email: griyariasasmara@gmail.com</p>
            </td>
        </tr>
    </table>
    <div class="GarisKop"></div>
    <div class="report-title">KATALOG VISUAL PILIHAN BAJU PENGANTIN</div>

    @php $isFirst = true; @endphp
    @foreach($dataPaket as $namaPaket => $daftarBaju)
        {{-- Paket pertama ($isFirst) langsung nempel di halaman 2 di bawah KOP tanpa jeda lembar kosong --}}
        <div class="{{ $isFirst ? 'paket-wrapper' : 'paket-wrapper-pagebreak' }}">
            <div class="package-header">PAKET WEDDING: {{ $namaPaket }}</div>
            
            <table class="grid-table">
                @php $count = 0; $isFirst = false; @endphp
                @foreach($daftarBaju as $row)
                    @if($count % 3 == 0) <tr> @endif
                    <td>
                        <div class="img-frame">
                            @php $path = public_path('storage/' . $row->foto_gown); @endphp
                            @if($row->foto_gown && file_exists($path))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($path)) }}" alt="Foto Gaun Pengantin">
                            @else
                                <div style="padding-top: 80px; color: #94a3b8; font-size: 9pt;">No Image</div>
                            @endif
                        </div>
                        <div class="desc-text">
                            <span style="font-size: 10pt; font-weight: bold; color: #1a202c;">{{ $row->nama_gown }}</span><br>
                            <span class="badge" style="background-color: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-size: 7.5pt; font-weight: bold; color: #4a5568;">
                                Stok Tersedia: {{ $row->stok }}
                            </span>
                            <div style="margin-top: 5px; color: #4a5568;">
                                {{ $row->deskripsi_gown ?? '-' }}
                            </div>
                        </div>
                    </td>
                    @php $count++; @endphp
                    @if($count % 3 == 0) </tr> @endif
                @endforeach

                {{-- Menutup sisa kolom kosong agar struktur HTML tabel tetap valid dan simetris --}}
                @if($count % 3 != 0)
                    @for($sisa = 0; $sisa < (3 - ($count % 3)); $sisa++)
                        <td style="border: none !important; background: transparent;"></td>
                    @endfor
                    </tr>
                @endif
            </table>
        </div>
    @endforeach

</body>
</html>