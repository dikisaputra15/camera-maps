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
                <h1>Edit Pelanggan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="/home">Dashboard</a></div>
                    <div class="breadcrumb-item">Edit Pelanggan</div>
                </div>
            </div>

             <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pelanggan.update', $pelanggan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>ID Pelanggan</label>
                            <input type="text" name="id_pel" value="{{ old('id_pel', $pelanggan->id_pel) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>No Meter</label>
                            <input type="text" name="no_meter" value="{{ old('no_meter', $pelanggan->no_meter) }}" class="form-control" required>
                        </div>

                        <button class="btn btn-primary">Simpan</button>
                        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>

        </section>

    </div>
@endsection

@push('scripts')

@endpush
