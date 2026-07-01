<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Booking Wedding - #{{ $booking->id }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Pengaturan Dasar */
        body {
            font-family: 'Times New Roman', serif;
            color: #000;
            background: white;
            line-height: 1.3;
        }

        /* Hilangkan Border di Sini agar tidak ada kotak luar */
        .print-container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            padding: 10px;
            border: none;
        }

        /* Kop Surat */
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .kop-surat h2 {
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            font-size: 20px;
        }

        .kop-surat p {
            margin: 1px 0;
            font-size: 12px;
        }

        .document-title {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-size: 16px;
        }

        /* Tabel Data Padat */
        .table-data {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .table-data td {
            padding: 5px 5px;
            vertical-align: top;
            border-bottom: 1px dotted #ccc;
            font-size: 14px;
        }

        .label-cell {
            width: 30%;
            font-weight: bold;
            text-transform: uppercase;
        }

        .separator {
            width: 3%;
            text-align: center;
        }

        /* Box Kesepakatan */
        .agreement-box {
            font-size: 12px;
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #000;
            text-align: justify;
        }

        /* TANDA TANGAN SEJAJAR */
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }

        .signature-box {
            text-align: center;
            width: 40%;
            display: inline-block;
            vertical-align: top;
        }

        .signature-box-left {
            float: left;
        }

        .signature-box-right {
            float: right;
        }

        .signature-space {
            height: 75px;
        }

        .clear {
            clear: both;
        }

        /* MENGHILANGKAN TOMBOL SAAT DIPRINT */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .print-container {
                padding: 0;
                width: 100%;
                border: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="print-container">
        {{-- KOP SURAT --}}
        <div class="kop-surat">
            <h2>Griya Rias Asmara</h2>
            <p><strong>Management Wedding Organizer & Rias Pengantin</strong></p>
            <p>Alamat: Dukuh Kendaga Gg. Bons Royen 4 RT 02 RW 11
                Larangan - Brebes Jawa Tengah 52262</p>
            <p>WhatsApp: 083861300111 / 085866659930</p>
        </div>

        <h4 class="document-title">Formulir Pendaftaran Booking</h4>

        {{-- DATA UTAMA SESUAI DETAIL/EDIT --}}
        <table class="table-data">
            <tr>
                <td class="label-cell">Nama Pemesan / CP</td>
                <td class="separator">:</td>
                <td>{{ $booking->customer_name }}</td>
            </tr>
            <tr>
                <td class="label-cell">WhatsApp / No. Telp</td>
                <td class="separator">:</td>
                <td>{{ $booking->whatsapp_number }}</td>
            </tr>
            <tr>
                <td class="label-cell">Nama Pengantin</td>
                <td class="separator">:</td>
                <td style="font-weight: bold;">{{ $booking->bride_groom_name }}</td>
            </tr>
            <tr>
                <td class="label-cell">Facebook / Instagram</td>
                <td class="separator">:</td>
                <td>{{ $booking->facebook_name ?? '-' }} / {{ $booking->instagram_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Nama Orang Tua</td>
                <td class="separator">:</td>
                <td>{{ $booking->parent_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Tanggal Pelaksanaan</td>
                <td class="separator">:</td>
                <td style="color: red; font-weight: bold;">
                    {{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('dddd, D MMMM Y') }}</td>
            </tr>
            <tr>
                <td class="label-cell">Durasi Acara</td>
                <td class="separator">:</td>
                <td>{{ $booking->event_duration }} Hari</td>
            </tr>
            <tr>
                <td class="label-cell">Alamat Lokasi</td>
                <td class="separator">:</td>
                <td>{{ $booking->event_address }}</td>
            </tr>
            <tr>
                <td class="label-cell">Paket Utama</td>
                <td class="separator">:</td>
                <td><strong>{{ $booking->package_name }}</strong></td>
            </tr>

            {{-- BAGIAN BARU: Add-ons Dinamis --}}
            <tr>
                <td class="label-cell">Item Tambahan</td>
                <td class="separator">:</td>
                <td>
                    @if($booking->addOns->isNotEmpty())
                        <ul style="margin: 0; padding-left: 15px;">
                            @foreach($booking->addOns as $add)
                                <li>{{ $add->nama_item }}</li>
                            @endforeach
                        </ul>
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <td class="label-cell">Total Harga</td>
                <td class="separator">:</td>
                <td style="font-weight: bold;">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label-cell">Catatan Khusus</td>
                <td class="separator">:</td>
                <td>{{ $booking->notes ?? '-' }}</td>
            </tr>
        </table>

        {{-- KESEPAKATAN --}}
        <div class="agreement-box">
            <strong>KESEPAKATAN BOOKING:</strong><br>
            Dengan adanya form ini telah terjadi kesepakatan antara calon pengantin dan <strong>GRIYA RIAS ASMARA
                MANAGEMENT</strong> maksimal DP 50% setelah fitting dan jika calon pengantin membatalkan jadwal yang
            sudah masuk dengan cara sepihak, apapun alasan dan keadaan Uang yang sudah masuk dianggap
            <strong>HANGUS</strong> jika ada perubahan jadwal atau permintaan property tanpa pemberitahuan paling lambat
            15 hari sebelum hari H itu semua diluar tanggung jawab <strong>TEAM GRIYA RIAS ASMARA MANAGEMENT</strong>.
        </div>

        {{-- TANDA TANGAN BERSANDINGAN --}}
        <div class="signature-section">
            <div class="signature-box signature-box-left">
                <p>Calon Pengantin,</p>
                <div class="signature-space"></div>
                <p><strong>( {{ $booking->bride_groom_name }} )</strong></p>
            </div>
            <div class="signature-box signature-box-right">
                <p>Team Management,</p>
                <div class="signature-space"></div>
                <p><strong>( ........................................ )</strong></p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="mt-4 text-center text-muted" style="font-size: 9px; border-top: 1px solid #eee; padding-top: 5px;">
            Dokumen sah dicetak otomatis melalui Sistem Manajemen <strong>SysGRA</strong> pada
            {{ now()->translatedFormat('d F Y H:i') }} WIB
        </div>
    </div>

</body>

</html>