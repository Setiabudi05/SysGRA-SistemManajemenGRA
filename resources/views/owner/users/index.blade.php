@extends('layouts.master')
@section('title', 'Kelola Data User')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">User</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Kelola Data User</h3>
                <p class="text-muted mb-0 small">Manajemen akun pengguna dan hak akses sistem.</p>
            </div>
            
            <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center gap-2 mt-3 mt-md-0">
                <select id="filter-category" class="form-select shadow-sm" style="width: 220px;">
                    <option value="">-- Semua Kategori Akun --</option>
                    <option value="karyawan">Karyawan (Admin & Kru)</option>
                    <option value="pelanggan">User (Pelanggan)</option>
                </select>
                <a href="{{ route('owner.users.create') }}" class="btn btn-primary shadow-sm px-3 fw-bold">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah User
                </a>
            </div>
        </div>
    </div>
    <hr>
</div>

<section class="section">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle users-table" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th width="15%">Jabatan</th>
                            <th width="12%">Role</th>
                            <th class="text-center" width="150px">Action</th>
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
        let table = $('.users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('owner.users.data') }}",
                data: d => { d.category = $('#filter-category').val(); }
            },
            order: [[1, 'asc']],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                { data: 'name', class: 'fw-bold' },
                { data: 'email' },
                { data: 'jabatan' },
                { data: 'role' },
                { data: 'action', orderable: false, searchable: false, class: 'text-center' }
            ]
        });

        $('#filter-category').on('change', () => table.ajax.reload());
    });

    function hapusUser(id) {
        Swal.fire({
            title: "Apakah Anda Yakin?",
            text: "Akun user ini akan dihapus permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('owner/users') }}/${id}`,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: res => {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
                            $('.users-table').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: res.message });
                        }
                    }
                });
            }
        });
    }
</script>

@if(session('swal_success'))
<script>
    Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('swal_success') }}", timer: 1500, showConfirmButton: false });
</script>
@endif
@endpush