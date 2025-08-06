@extends('layouts.app')
@section('title', 'Pelanggan')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <style>
        /* Sembunyikan progress bar secara default */
        #progressBar {
            display: none;
        }
    </style>
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
                        <div class="mb-3 d-flex justify-content-center gap-3">
                            <form action="{{ route('plg.export') }}" method="GET" class="form-inline">
                                <select name="bulan" class="form-control form-control-sm mr-2" required>
                                    <option value="">Pilih Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                    @endfor
                                </select>

                                <select name="tahun" class="form-control form-control-sm mr-2" required>
                                    <option value="">Pilih Tahun</option>
                                    @for ($year = now()->year; $year >= 2020; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>

                                <button class="btn btn-success btn-sm" type="submit">Export</button>
                            </form>

                        </div>
                        <div class="mb-3 d-flex justify-content-end gap-3">
                            <a href="{{ route('pelanggan.create') }}" class="btn btn-primary btn-sm mr-2">Tambah data</a>

                            <button type="button" class="btn btn-info btn-sm mr-2" data-toggle="modal" data-target="#importModal">
                                Import Data
                            </button>

                            <button id="delete-selected" class="btn btn-danger btn-sm">Delete All</button>
                        </div>

                        <div class="table-responsive">
                            <table id="userTable" class="display">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
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

    <!-- Modal Import Data -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Pelanggan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="fileInput">Pilih File Excel</label>
                            <input type="file" name="file" id="fileInput" required class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </form>
                    <progress id="progressBar" value="0" max="100" style="width:100%; margin-top: 10px;"></progress>
                    <div id="status" class="mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const statusElement = document.getElementById('status');
            const progressBarElement = document.getElementById('progressBar');

            // Tampilkan progress bar dan reset nilainya
            progressBarElement.style.display = 'block'; // Tampilkan progress bar
            progressBarElement.value = 0; // Reset progress bar
            statusElement.innerText = 'Mengunggah...'; // Tampilkan status awal

            const form = new FormData(this);
            const xhr = new XMLHttpRequest();

            // Mengatur token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            xhr.open("POST", "{{ route('plg.import') }}", true);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken); // Tambahkan header CSRF token

            // --- Penanganan Progress Upload ---
            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percent = (e.loaded / e.total) * 100;
                    progressBarElement.value = percent;
                    statusElement.innerText = `Mengunggah... ${Math.round(percent)}%`;
                }
            };

            // --- Penanganan Respons Server ---
            xhr.onload = function() {
                // Sembunyikan progress bar setelah selesai (baik sukses/gagal)
                progressBarElement.style.display = 'none';

                // Periksa status HTTP sebelum memproses respons
                if (xhr.status >= 200 && xhr.status < 300) {
                    // Sukses (status 2xx)
                    try {
                        const response = JSON.parse(xhr.responseText);
                        statusElement.innerText = response.message || 'Unggah selesai!';
                         // Tutup modal setelah sukses
                        $('#importModal').modal('hide');
                        // Opsional: Muat ulang tabel data setelah import sukses
                        // SweetAlert berhasil
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message || 'Data berhasil diimpor!',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        });

                        $('#userTable').DataTable().ajax.reload(null, false);

                    } catch (err) {
                        // Jika respons bukan JSON (misalnya HTML dari error server)
                        statusElement.innerText = 'Unggahan selesai, tapi respons tidak valid. Periksa server Anda.';
                        console.error('Gagal mem-parsing JSON:', err, xhr.responseText);
                    }
                } else if (xhr.status === 413) {
                    // Error 413 (Content Too Large)
                    statusElement.innerText = 'Unggahan gagal: Ukuran file terlalu besar. Mohon batasi ukuran file Anda.';
                } else if (xhr.status === 419) {
                    // Error 419 (CSRF Token Mismatch) - sering terjadi jika sesi habis
                    statusElement.innerText = 'Unggahan gagal: Sesi Anda telah berakhir. Mohon refresh halaman dan coba lagi.';
                    console.error('CSRF Token Mismatch (419):', xhr.responseText);
                }
                else {
                    // Error lainnya (400, 500, dll)
                    let errorMessage = `Unggahan gagal. Kode error: ${xhr.status}.`;
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (err) {
                        // Respons bukan JSON, mungkin HTML dari server
                    }
                    statusElement.innerText = errorMessage;
                    console.error('Error server:', xhr.status, xhr.responseText);
                }
            };

            // --- Penanganan Error Jaringan ---
            xhr.onerror = function() {
                // Sembunyikan progress bar jika ada error jaringan
                progressBarElement.style.display = 'none';
                statusElement.innerText = 'Unggahan gagal: Terjadi kesalahan jaringan. Periksa koneksi internet Anda.';
            };

            xhr.send(form);
        });

        // Event listener untuk mereset form dan status ketika modal ditutup
        $('#importModal').on('hidden.bs.modal', function () {
            const uploadForm = document.getElementById('uploadForm');
            uploadForm.reset(); // Reset form
            document.getElementById('progressBar').style.display = 'none'; // Sembunyikan progress bar
            document.getElementById('status').innerText = ''; // Kosongkan status
        });
    </script>

    {{-- Script untuk DataTable dan SweetAlert --}}
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
                columns: [
                    {
                        data: 'id',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `<input type="checkbox" class="row-checkbox" value="${row.id}">`;
                        }
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'id_pel', name: 'id_pel' },
                    { data: 'no_meter', name: 'no_meter' },
                    { data: 'nama', name: 'nama' },
                    { data: 'tarif', name: 'tarif' },
                    { data: 'daya', name: 'daya' },
                    { data: 'jenis_layanan', name: 'jenis_layanan' },
                    { data: 'alamat', name: 'alamat' },
                    { data: 'rt', name: 'rt' },
                    { data: 'rw', name: 'rw' },
                    { data: 'hasil_kunjungan', name: 'hasil_kunjungan' },
                    { data: 'telp', name: 'telp' },
                    { data: 'kabel_sl', name: 'kabel_sl' },
                    { data: 'jenis_sambungan', name: 'jenis_sambungan' },
                    { data: 'merk_mcb', name: 'merk_mcb' },
                    { data: 'ampere_mcb', name: 'ampere_mcb' },
                    { data: 'gardu', name: 'gardu' },
                    { data: 'gambar_kwh', name: 'gambar_kwh' },
                    { data: 'gambar_rumah', name: 'gambar_rumah' },
                    { data: 'gambar_sr', name: 'gambar_sr' },
                    { data: 'gambar_tiang', name: 'gambar_tiang' },
                    { data: 'difoto_oleh', name: 'difoto_oleh' },
                    { data: 'tanggal_foto', name: 'tanggal_foto' },
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
                                    Swal.fire({
                                        title: "Dihapus!",
                                        text: "Record telah dihapus.",
                                        icon: "success",
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    $('#userTable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: "Gagal menghapus record.",
                                        icon: "error",
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Terjadi kesalahan saat menghapus data.",
                                    icon: "error",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                console.error("AJAX Error:", status, error, xhr.responseText);
                            }
                        });
                    }
                });
            });
        });
    </script>

    <script>
        // Select/Deselect all checkboxes
$('#select-all').on('click', function () {
    $('.row-checkbox').prop('checked', this.checked);
});

// Multiple delete
$('#delete-selected').on('click', function () {
    const selectedIds = $('.row-checkbox:checked').map(function () {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        Swal.fire({
            title: "Tidak ada data terpilih",
            text: "Pilih minimal satu baris untuk dihapus.",
            icon: "warning"
        });
        return;
    }

    Swal.fire({
        title: "Yakin ingin menghapus?",
        text: "Data terpilih akan dihapus secara permanen.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('pelanggan.multiple-delete') }}",
                method: 'POST',
                data: {
                    ids: selectedIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: response.message,
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#userTable').DataTable().ajax.reload(null, false);
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: "Gagal!",
                        text: "Terjadi kesalahan saat menghapus data.",
                        icon: "error"
                    });
                }
            });
        }
    });
});

    </script>
@endpush
