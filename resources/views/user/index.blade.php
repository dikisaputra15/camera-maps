@extends('layouts.app')
@section('title', 'Users')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet"
        href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $title ?? '' }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="/home">Dashboard</a></div>
                    <div class="breadcrumb-item">Data User</div>
                </div>
            </div>

            <div id="session" data-session="{{ session('success') }}"></div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-end gap-3">
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm ml-2">Tambah data</a>
                        </div>
                        <div class="table-responsive">
                        <table id="userTable" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                        </div>
                    </div>

                </div>
            </div>

        </section>

    </div>
@endsection

@push('scripts')

    <script>
        $(document).ready(function() {

            let session = $('#session').data('session');

            if (session) {
                Swal.fire({
                    title: "Sukses!",
                    text: session,
                    icon: "success",
                    timer: 3000,
                    showConfirmButton: true
                });
            }

            // table data
            $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });


            // Event listener untuk tombol hapus
            $('#userTable').on('click', '.delete-btn', function () {
                var userId = $(this).data('id');

                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Data akan dihapus secara permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/users/' + userId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response){
                            if(response.success == 1){
                                alert("Record deleted.");
                                var oTable = $('#userTable').dataTable();
                                oTable.fnDraw(false);
                            }else{
                                    alert("Invalid ID.");
                                }
                            },

                        });
                    }
                });
            });

            // Buka modal & set data
$('#userTable').on('click', '.edit-role-btn', function () {
    var userId = $(this).data('id');
    var currentRole = $(this).data('role');

    $('#edit_user_id').val(userId);
    $('#edit_role').val(currentRole);
    $('#modalUbahRole').modal('show');
});

// Submit update role
$('#formUbahRole').on('submit', function (e) {
    e.preventDefault();
    var userId = $('#edit_user_id').val();
    var role = $('#edit_role').val();

    $.ajax({
        url: '/users/' + userId + '/update-role',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            role: role
        },
        success: function (response) {
            $('#modalUbahRole').modal('hide');
            $('#userTable').DataTable().ajax.reload(null, false);

            Swal.fire({
                title: 'Sukses!',
                text: response.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        },
        error: function () {
            alert('Gagal mengubah role.');
        }
    });
});



        });
    </script>


@endpush
