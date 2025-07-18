@extends('layouts.app')
@section('title', 'Pelanggan')

@push('style')
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
                    <div class="breadcrumb-item">Cari Pelanggan</div>
                </div>
            </div>

          <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3"> {{-- Gunakan col-md-6 agar input lebih lebar --}}
                                    <label for="search_query" class="form-label">ID Pelanggan atau No Meter</label>
                                    <input type="text" class="form-control @error('search_query') is-invalid @enderror"
                                        id="search_query" name="search_query" value="{{ old('search_query') }}" required>
                                    @error('search_query')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-4"> {{-- Sesuaikan margin top --}}
                                    <button type="button" class="btn btn-primary" id="searchBtn">Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="text-center my-3" id="loading" style="display: none;">
                        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="card-body" id="result">
                    </div>
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
        let searchQuery = $('#search_query').val(); // Ambil nilai dari satu input

        $('#loading').show();
        $('#result').html(''); // Kosongkan hasil sebelumnya

        $.ajax({
            url: '{{ route("search.pelanggan") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                search_query: searchQuery // Kirim satu parameter
            },
            success: function (response) {
                $('#loading').hide();
                if (response.success) {
                    $('#result').html(response.html);
                } else {
                    $('#result').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function (xhr, status, error) {
                $('#loading').hide();
                // Lebih detail untuk error AJAX
                let errorMessage = 'Terjadi kesalahan saat mengambil data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                $('#result').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                console.error("AJAX Error:", status, error, xhr.responseText);
            }
        });
    });
</script>
@endpush
