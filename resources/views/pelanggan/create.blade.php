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
                    <div class="breadcrumb-item">Add Pelanggan</div>
                </div>
            </div>

          <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('pelanggan.store') }}" method="POST">
                            @csrf
                            <div class="row">


                                <div class="col-md-12 mb-3">
                                    <label for="kendaraan" class="form-label">ID Pelanggan</label>
                                    <input type="text" class="form-control @error('id_pel') is-invalid @enderror"
                                        id="id_pel" name="id_pel" value="{{ old('id_pel') }}" required>
                                    @error('id_pel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                 <div class="col-md-12 mb-3">
                                    <label for="kendaraan" class="form-label">No Meter</label>
                                    <input type="text" class="form-control @error('no_meter') is-invalid @enderror"
                                        id="no_meter" name="no_meter" value="{{ old('no_meter') }}" required>
                                    @error('no_meter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>

                    </div>

                </div>
            </div>

        </section>

    </div>
@endsection

@push('scripts')

@endpush
