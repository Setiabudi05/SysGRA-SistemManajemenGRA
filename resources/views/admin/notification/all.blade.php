@extends('layouts.master') {{-- Sesuaikan dengan layout Admin Anda --}}
@section('title', 'Notifikasi')

@section('content')
<div class="page-content">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="mb-4 fw-bold">Pusat Notifikasi Admin</h4>
            
            <div class="list-group shadow-sm rounded">
                @forelse($allNotif as $notif)
                    <div class="list-group-item d-flex align-items-start py-3" 
                         style="border-left: 4px solid {{ $notif->read_at ? '#cbd5e1' : '#435ebe' }};">
                        
                        <div class="avatar bg-light-info me-3 p-2 rounded">
                            {{-- Ikon dinamis berdasarkan judul --}}
                            <i class="bi {{ str_contains($notif->data['judul'], 'Booking') ? 'bi-cart-check' : 'bi-cash-stack' }} fs-4 text-info"></i>
                        </div>

                        <div class="w-100">
                            <div class="d-flex justify-content-between">
                                <h6 class="fw-bold mb-1">{{ $notif->data['judul'] ?? 'Notifikasi' }}</h6>
                                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-2 text-secondary small">{{ $notif->data['pesan'] }}</p>
                            
                            {{-- Tombol Aksi Cepat ke Detail Transaksi --}}
                            <a href="{{ $notif->data['url'] ?? '#' }}" class="btn btn-sm btn-outline-primary fw-bold">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>
                        <p>Tidak ada notifikasi baru untuk Admin.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $allNotif->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection