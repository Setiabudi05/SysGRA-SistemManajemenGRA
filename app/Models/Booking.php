<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'whatsapp_number',
        'bride_groom_name',
        'parent_name',
        'facebook_name',
        'instagram_name',
        'event_address',
        'latitude',
        'longitude',
        'event_date',
        'event_duration',
        'paket_id',
        'package_name',
        'package_price',
        'notes',
        'status',
        'another_column_name'
    ];

    protected $casts = [
        'package_price' => 'integer',
        'event_date' => 'date',
    ];

    protected $appends = ['total_bayar', 'sisa_tagihan'];

    // Relasi ke tabel master Add-ons melalui tabel pivot 'add_ons_booking'
    // Pastikan di kedua Model (Booking & AddOn)
public function addOns()
{
    return $this->belongsToMany(AddOn::class, 'add_ons_booking', 'booking_id', 'add_on_id');
}

    // Relasi ke Paket
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id', 'id');
    }

    // Relasi ke JadwalPengantin
    public function jadwal()
    {
        return $this->hasOne(JadwalPengantin::class, 'pesanan_id');
    }

    // Relasi ke Pembayaran
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'pesanan_id');
    }

    // Logic untuk menghitung total pembayaran yang sudah masuk

    // Tambahkan Accessor untuk Total Harga
    public function getTotalHargaAttribute()
    {
        // Harga Paket
        $hargaPaket = $this->paket->harga; // Pastikan ini mengambil harga paket asli

        // Harga Add-ons (Pastikan di-sum hanya sekali)
        $hargaAddOns = $this->addOns->sum('harga');

        return $hargaPaket + $hargaAddOns;
    }
    // Logic untuk menghitung sisa tagihan
    public function getSisaTagihanAttribute()
    {
        $sisa = (int) $this->package_price - $this->total_bayar;
        return $sisa < 0 ? 0 : $sisa;
    }

    // Tambahkan tepat di bawah relasi pembayarans() atau di dekat method lainnya
    public function getTotalBayarAttribute()
    {
        // Menghitung total pembayaran yang statusnya sukses/lunas
        return (int) $this->pembayarans()
            ->whereIn('status_pembayaran', ['success', 'lunas', null])
            ->sum('jumlah_bayar');
    }

    // Auto-update jadwal saat status booking berubah menjadi dikonfirmasi
    protected static function booted()
    {
        static::updated(function ($booking) {
            if (in_array($booking->status, ['CONFIRMED', 'COMPLETED'])) {
                $date = Carbon::parse($booking->event_date);

                JadwalPengantin::updateOrCreate(
                    ['pesanan_id' => $booking->id],
                    [
                        'nama'         => $booking->bride_groom_name,
                        'alamat'       => $booking->event_address,
                        'paket_id'     => $booking->paket_id,
                        'tanggal_awal' => $booking->event_date,
                        'bulan'        => $date->format('F'),
                        'tahun'        => $date->format('Y'),
                        'keterangan'   => $booking->notes ?? '-',
                        'is_manual'    => 0
                    ]
                );
            }
        });
    }
}
