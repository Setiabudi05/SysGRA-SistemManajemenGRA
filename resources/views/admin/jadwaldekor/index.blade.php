@extends('layouts.master')
@section('title', 'Data Jadwal Dekor')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    {{-- CSS Global untuk tema SysGRA (Header Gelap & Rapat Ke Atas) --}}
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-7">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Jadwal Dekorasi</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Data Jadwal Dekorasi</h3>
                    <p class="text-muted mb-0">Kelola logistik dan visualisasi jadwal dekorasi lapangan.</p>
                </div>

                <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center mt-3 mt-md-0 gap-2">
                    <button id="btn-print" class="btn btn-secondary shadow-sm">
                        <i class="bi bi-printer"></i> Cetak Jadwal
                    </button>
                    <a href="{{ route('admin.jadwaldekor.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                        <i class="bi bi-plus-lg"></i> Tambah Jadwal
                    </a>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <h5 class="card-title mb-0 me-auto">Log Visual Dekorasi</h5>
                    
                    {{-- Filter Bulan --}}
                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-group text-muted">Bulan:</span>
                        <select id="filter-bulan" class="form-select form-select-sm shadow-sm">
                            <option value="">Semua</option>
                            @foreach (['Januari','Februari','Maret','April','Mei','Juni',
                                       'Juli','Agustus','September','Oktober','November','Desember'] as $i => $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Tahun --}}
                    <div class="d-flex align-items-center gap-2">
                        <span class="filter-group text-muted">Tahun:</span>
                        <select id="filter-tahun" class="form-select form-select-sm shadow-sm">
                            <option value="">Semua</option>
                            @for ($y = date('Y'); $y <= date('Y') + 5; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="jadwal-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Bulan</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Paket</th>
                                <th class="text-center">Foto</th>
                                <th>Deskripsi</th>
                                <th class="text-center" width="120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
    // 1. Definisikan Nama Bulan dan Waktu Sekarang
    const monthNames = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
    let now = new Date();

    // 2. Ambil parameter dari URL browser (kiriman redirect dari Controller)
    const urlParams = new URLSearchParams(window.location.search);
    const getBulan = urlParams.get('bulan');
    const getTahun = urlParams.get('tahun');

    /**
     * LOGIKA PRIORITAS FILTER:
     * 1. Jika ada parameter di URL (habis Edit/Tambah), gunakan itu.
     * 2. Jika baru buka fitur (URL bersih), set otomatis ke bulan & tahun sekarang.
     */
    if (getBulan) {
        $('#filter-bulan').val(getBulan);
    } else {
        // Otomatis pilih bulan sekarang jika URL bersih
        $('#filter-bulan').val(monthNames[now.getMonth()]);
    }

    if (getTahun) {
        $('#filter-tahun').val(getTahun);
    } else {
        // Otomatis pilih tahun sekarang jika URL bersih
        $('#filter-tahun').val(now.getFullYear());
    }

    // 3. Inisialisasi DataTable
    let table = $('#jadwal-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.jadwaldekor.data') }}",
            data: function (d) {
                // Mengambil nilai dari dropdown yang sudah terisi otomatis di atas
                d.bulan = $('#filter-bulan').val();
                d.tahun = $('#filter-tahun').val();
            }
        },
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        pageLength: 10,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'bulan', name: 'bulan' },
            { data: 'nama', name: 'nama', class: 'fw-bold' },
            { data: 'alamat', name: 'alamat' },
            { data: 'paket', name: 'paket' },
            { data: 'foto', name: 'foto', orderable: false, searchable: false, class: 'text-center' },
            { data: 'deskripsi', name: 'deskripsi' },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
        ]
    });

    // Reload saat filter dropdown diubah manual
    $('#filter-bulan, #filter-tahun').change(function () { table.draw(); });

    // Fungsi Print Laporan
    $('#btn-print').on('click', function () {
        let url = "{{ route('admin.jadwaldekor.print') }}?bulan=" + $('#filter-bulan').val() + "&tahun=" + $('#filter-tahun').val();
        window.open(url, "_blank");
    });
});

        // FUNGSI HAPUS DENGAN TIMER
        function hapusJadwal(id) {
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Data visual dan logistik akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/jadwaldekor/destroy') }}/" + id, 
                        type: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (res) {
                            Swal.fire({
                                icon: res.success ? 'success' : 'error',
                                title: res.success ? 'Berhasil!' : 'Gagal!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false // Hilangkan tombol OK agar timer bekerja
                            });
                            
                            if (res.success) {
                                $('#jadwal-table').DataTable().ajax.reload(null, false);
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Gagal menghubungi server.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        }
    </script>

    {{-- SweetAlert Notifikasi untuk Redirect Create/Edit --}}
    @if(session('swal_success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('swal_success') }}",
                timer: 1500,
                showConfirmButton: false // Timer aktif tanpa klik OK
            });
        });
    </script>
    @endif
@endpush