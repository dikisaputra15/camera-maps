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
                    <div class="breadcrumb-item">Search Pelanggan</div>
                </div>
            </div>

          <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST">
                            @csrf
                            <div class="row">


                                <div class="col-md-4 mb-3">
                                    <label for="kendaraan" class="form-label">ID Pelanggan</label>
                                    <input type="text" class="form-control @error('id_pel') is-invalid @enderror"
                                        id="id_pel" name="id_pel" value="{{ old('id_pel') }}" required>
                                    @error('id_pel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                 <div class="col-md-4 mb-3">
                                    <label for="kendaraan" class="form-label">No Meter</label>
                                    <input type="text" class="form-control @error('no_meter') is-invalid @enderror"
                                        id="no_meter" name="no_meter" value="{{ old('no_meter') }}" required>
                                    @error('no_meter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                             <div class="col-md-4 mt-4">
                                <button type="button" class="btn btn-primary" id="searchBtn">Filter</button>
                            </div>

                            </div>

                        </form>

                    </div>

                    <div class="text-center my-3" id="loading" style="display: none;">
                        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <div id="result" class="mt-4"></div>
                </div>
            </div>

        </section>

    </div>
@endsection

@push('scripts')
<style>
    .spinner-border {
        animation: spin 1s linear infinite, pulse 1.5s ease-in-out infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
<script>
    $('#searchBtn').click(function () {
        let id_pel = $('#id_pel').val();
        let no_meter = $('#no_meter').val();

        $('#loading').show();
        $('#result').html('');

        $.ajax({
            url: '{{ route("search.pelanggan") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id_pel: id_pel,
                no_meter: no_meter
            },
            success: function (response) {
                $('#loading').hide();
                if (response.success) {
                    $('#result').html(response.html);
                } else {
                    $('#result').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function () {
                $('#loading').hide();
                $('#result').html('<div class="alert alert-danger">Terjadi kesalahan saat mengambil data.</div>');
            }
        });
    });
</script>

@endpush

