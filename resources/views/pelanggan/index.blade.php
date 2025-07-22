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
                    <div class="breadcrumb-item">Data Pelanggan</div>
                </div>
            </div>

            <div id="session" data-session="{{ session('success') }}"></div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-end gap-3">
                            <a href="{{ route('pelanggan.create') }}" class="btn btn-primary btn-sm ml-2">Tambah data</a>
                        </div>
                        <div class="table-responsive">
                        <table id="userTable" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Id Pel</th>
                                    <th>No Meter</th>
                                    <th>Gambar KWH</th>
                                    <th>Gambar Rumah</th>
                                    <th>Verified</th>
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
                ajax: "{{ route('pelanggan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_pel',
                        name: 'id_pel'
                    },
                    {
                        data: 'no_meter',
                        name: 'no_meter'
                    },
                    {
                        data: 'gambar_kwh',
                        name: 'gambar_kwh'
                    },
                    {
                        data: 'gambar_rumah',
                        name: 'gambar_rumah'
                    },
                    {
                        data: 'verified',
                        name: 'verified',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // Render checkbox based on the 'verified' status
                            let checked = data ? 'checked' : '';
                            return `<input type="checkbox" class="verified-checkbox" data-id="${row.id}" ${checked}>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

        $('#userTable').on('change', '.verified-checkbox', function () {
                const userId = $(this).data('id');
                const isVerified = $(this).is(':checked');
                const $thisCheckbox = $(this);

                $.ajax({
                    url: `/pelanggan/${userId}/update-verified`,
                    type: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        verified: isVerified
                    }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                         $('#userTable').DataTable().ajax.reload(null, false);
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memperbarui status verifikasi.',
                        });
                        $thisCheckbox.prop('checked', !isVerified);
                    }
                });
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
                            url: '/pelanggan/' + userId,
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

        });
    </script>


@endpush
