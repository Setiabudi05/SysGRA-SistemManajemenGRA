@extends('layouts.user')

@section('title', 'Notifikasi')

{{-- TAMBAHKAN INI UNTUK MEMULAI SECTION --}}
@section('content')

<div class="container">
    <h4 class="mb-4">Daftar Semua Notifikasi</h4>
    
    <div class="list-group">
        @forelse($allNotif as $notif)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">{{ $notif->data['judul'] ?? 'Notifikasi' }}</h6>
                    <p class="mb-1 text-muted">{{ $notif->data['pesan'] }}</p>
                    <small class="text-secondary">{{ $notif->created_at->diffForHumans() }}</small>
                </div>
                @if($notif->read_at == null)
                    <span class="badge bg-primary">Baru</span>
                @else
                    <span class="badge bg-secondary">Sudah dibaca</span>
                @endif
            </div>
        @empty
            <p class="text-center text-muted">Belum ada riwayat notifikasi.</p>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $allNotif->links() }}
    </div>
</div>

{{-- TAMBAHKAN INI UNTUK MENUTUP SECTION --}}
@endsection