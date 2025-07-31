@extends('layouts.app')
@section('title', 'Pelanggan')

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

                         <div class="mb-3">
                            <button class="btn btn-warning btn-icon upload-btn d-inline ml-2">
                                <i class="fas fa-upload"></i> Import Excel
                            </button>
                            <a href="{{ route('plg.export') }}" class="btn btn-success btn-sm me-2">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>

                        <div class="table-responsive">
                        <table id="userTable" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Id Pel</th>
                                    <th>No Meter</th>
                                    <th>Nama</th>
                                    <th>Tarif</th>
                                    <th>Daya</th>
                                    <th>Jenis Layanan</th>
                                    <th>Alamat</th>
                                    <th>RT</th>
                                    <th>RW</th>
                                    <th>Hasil Kunjungan</th>
                                    <th>Telephone</th>
                                    <th>Kabel SL</th>
                                    <th>Jenis Sambungan</th>
                                    <th>Merek MCB</th>
                                    <th>Ampere MCB</th>
                                    <th>Gardu MCB</th>
                                    <th>Gambar KWH</th>
                                    <th>Gambar Rumah</th>
                                    <th>Gambar SR</th>
                                    <th>Gambar Tiang</th>
                                    <th>Difoto Oleh</th>
                                    <th>Tanggal Foto</th>
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

     <!-- Modal Upload -->
      <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Import File Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <form action="{{ route('plg.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File</label>
                            <input type="file" class="form-control" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".upload-btn").forEach(button => {
            button.addEventListener("click", function() {
                let uploadModal = new bootstrap.Modal(document.getElementById("uploadModal"));
                uploadModal.show();
            });
        });
    });
</script>

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
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'tarif',
                        name: 'tarif'
                    },
                    {
                        data: 'daya',
                        name: 'daya'
                    },
                    {
                        data: 'jenis_layanan',
                        name: 'jenis_layanan'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'rt',
                        name: 'rt'
                    },
                    {
                        data: 'rw',
                        name: 'rw'
                    },
                    {
                        data: 'hasil_kunjungan',
                        name: 'hasil_kunjungan'
                    },
                    {
                        data: 'telp',
                        name: 'telp'
                    },
                    {
                        data: 'kabel_sl',
                        name: 'kabel_sl'
                    },
                    {
                        data: 'jenis_sambungan',
                        name: 'jenis_sambungan'
                    },
                    {
                        data: 'merk_mcb',
                        name: 'merk_mcb'
                    },
                    {
                        data: 'ampere_mcb',
                        name: 'ampere_mcb'
                    },
                    {
                        data: 'gardu',
                        name: 'gardu'
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
                        data: 'gambar_sr',
                        name: 'gambar_sr'
                    },
                    {
                        data: 'gambar_tiang',
                        name: 'gambar_tiang'
                    },
                    {
                        data: 'difoto_oleh',
                        name: 'difoto_oleh'
                    },
                    {
                        data: 'tanggal_foto',
                        name: 'tanggal_foto'
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
