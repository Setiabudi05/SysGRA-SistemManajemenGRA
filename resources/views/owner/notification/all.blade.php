@extends('layouts.master')
@section('title', 'Notifikasi')

@section('content')
<div class="page-content">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">Daftar Notifikasi</h4>
            <div class="list-group">
                @forelse($allNotif as $notif)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $notif->data['judul'] ?? 'Info' }}</h6>
                            <p class="mb-1 text-secondary">
                                {{ $notif->data['pesan'] ?? 'Tidak ada pesan' }}
                            </p>
                            <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                        </div>
                        
                        {{-- Opsional: Tambahkan indikator visual berdasarkan pesan --}}
                        <div>
                            @if(str_contains($notif->data['pesan'] ?? '', 'BISA HADIR'))
                                <span class="badge bg-success">Hadir ✅</span>
                            @elseif(str_contains($notif->data['pesan'] ?? '', 'BERHALANGAN'))
                                <span class="badge bg-danger">Absen ❌</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">Belum ada notifikasi.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection