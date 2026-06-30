@extends('layouts.master')

@section('title', 'Semua Notifikasi')

@push('css')
    <style>
        .list-group-item {
            transition: all 0.2s ease-in-out;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .italic {
            font-style: italic;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <div class="row">
                <div class="col-12">
                    <h3 class="fw-bold text-dark mb-0">Semua Kotak Masuk Notifikasi</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mt-2 mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route(auth()->user()->role . '.dashboard') }}"
                                   style="text-decoration: none; color: #435ebe;">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Kotak Masuk</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content mt-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="list-group shadow-sm rounded">
                    @forelse($allNotif as $notif)
                        @php
                            // Cek apakah ini penugasan baru untuk memunculkan tombol konfirmasi
                            $isTugas = str_contains($notif->data['pesan'] ?? '', 'ditugaskan') || str_contains($notif->data['judul'] ?? '', 'Penugasan');
                            $statusKonfirmasi = $notif->data['status_konfirmasi'] ?? ($isTugas ? 'pending' : 'bukan_tugas');
                        @endphp

                        <div class="list-group-item list-group-item-action d-flex align-items-start py-3"
                            style="border-left: 4px solid {{ $notif->read_at ? '#cbd5e1' : '#435ebe' }}; margin-bottom: 5px; border-top: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7;">

                            <div class="avatar bg-light-primary me-3 p-2 rounded" style="min-width: 45px; text-align: center;">
                                <i class="bi {{ $notif->data['icon'] ?? 'bi-bell' }} fs-4 text-primary"></i>
                            </div>

                            <div class="w-100">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 fw-bold text-dark">{{ $notif->data['judul'] ?? 'Info Baru' }}</h6>
                                    <small class="text-muted small">{{ $notif->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-2 text-secondary small">{{ $notif->data['pesan'] }}</p>

                                {{-- LOGIKA TOMBOL INTERAKSI KONFIRMASI --}}
                                @if($statusKonfirmasi == 'pending')
                                    <div class="mt-2">
                                        <button onclick="konfirmasiTugas('{{ $notif->id }}', 'bisa')"
                                            class="btn btn-sm btn-success me-2 shadow-sm fw-bold">
                                            <i class="bi bi-check-lg"></i> Bisa Hadir
                                        </button>
                                        <button onclick="konfirmasiTugas('{{ $notif->id }}', 'tidak_bisa')"
                                            class="btn btn-sm btn-danger shadow-sm fw-bold">
                                            <i class="bi bi-x-lg"></i> Berhalangan
                                        </button>
                                    </div>
                                @elseif($statusKonfirmasi == 'bisa')
                                    <span class="badge bg-light-success text-success mt-1" style="padding: 0.4em 0.7em;">
                                        <i class="bi bi-check-circle-fill me-1"></i> Anda Mengonfirmasi: Bisa Hadir
                                    </span>
                                @elseif($statusKonfirmasi == 'tidak_bisa')
                                    <span class="badge bg-light-danger text-danger mt-1" style="padding: 0.4em 0.7em;">
                                        <i class="bi bi-x-circle-fill me-1"></i> Anda Mengonfirmasi: Berhalangan
                                    </span>
                                    @if(!empty($notif->data['alasan']))
                                        <div class="text-xs text-muted mt-2 border-start border-3 ps-2 italic">
                                            Alasan penolakan: "{{ $notif->data['alasan'] }}"
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-bell-slash fs-1 d-block mb-3 text-light"></i>
                            <h5 class="text-secondary fw-bold">Kotak masuk kosong</h5>
                            <p class="small mb-0">Belum ada riwayat pemberitahuan yang dikirim untuk Anda.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Link Pagination Menggunakan Bootstrap 5 --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $allNotif->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- SweetAlert2 Library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function konfirmasiTugas(notifId, jawaban) {
            if (jawaban === 'tidak_bisa') {
                // Pop-up jika Kru memilih BERHALANGAN (wajib isi alasan)
                Swal.fire({
                    title: 'Alasan Berhalangan',
                    input: 'textarea',
                    inputPlaceholder: 'Tuliskan alasan penolakan Anda di sini...',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Kirim Penolakan',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan wajib diisi agar Owner tahu kendala Anda!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        eksekusiKonfirmasi(notifId, jawaban, result.value);
                    }
                });
            } else {
                // Pop-up jika Kru memilih BISA HADIR
                Swal.fire({
                    title: 'Konfirmasi Kehadiran',
                    text: "Apakah Anda yakin BISA menghadiri agenda penugasan ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'Ya, Saya Bisa!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        eksekusiKonfirmasi(notifId, jawaban, '');
                    }
                });
            }
        }

        function eksekusiKonfirmasi(notifId, jawaban, alasan) {
            // Tampilkan animasi Loading Spinner saat fetch menembak data ke backend
            Swal.fire({
                title: 'Mohon Tunggu...',
                text: 'Sedang mengirimkan status konfirmasi Anda ke Owner.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Jalankan AJAX Request menggunakan Fetch API bawaan browser murni
            let urlRoute = "{{ route('kru.notification.respond', ':id') }}".replace(':id', notifId);

            fetch(urlRoute, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    jawaban: jawaban,
                    alasan: alasan
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal memproses ke server (Status: ' + response.status + ')');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false, // Hilangkan tombol OK/Konfirmasi
                            timer: 1000,              // Otomatis menutup dalam 1 detik
                        }).then(() => {
                            location.reload(); // Refresh halaman untuk mengubah tombol menjadi badge status terbaru
                        });
                    } else {
                        Swal.fire('Gagal!', data.message || 'Gagal memproses data.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error Sistem!', 'Tidak ada respons balik dari server. Sila cek tab Inspect Element -> Console.', 'error');
                });
        }
    </script>
@endpush