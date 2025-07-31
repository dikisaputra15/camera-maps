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

                                 <div class="col-md-12 mb-3">
                                    <label for="kendaraan" class="form-label">Nama</label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                        id="nama" name="nama" value="{{ old('nama') }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="kendaraan" class="form-label">Tarif</label>
                                    <input type="text" class="form-control @error('tarif') is-invalid @enderror"
                                        id="tarif" name="tarif" value="{{ old('tarif') }}" required>
                                    @error('tarif')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                 <div class="col-md-12 mb-3">
                                    <label for="kendaraan" class="form-label">Daya</label>
                                    <input type="text" class="form-control @error('daya') is-invalid @enderror"
                                        id="daya" name="daya" value="{{ old('daya') }}" required>
                                    @error('daya')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                 <div class="col-md-12 mb-3">
                                    <label for="kendaraan" class="form-label">Jenis Layanan</label>
                                   <select class="form-control" name="jenis_layanan">
                                        <option value="Prabayar">Prabayar</option>
                                        <option value="Pascabayar">Pascabayar</option>
                                   </select>
                                </div>

                                  <div class="col-md-12 mb-3">
                                    <label for="kendaraan" class="form-label">Alamat</label>
                                    <input type="text" class="form-control @error('alamat') is-invalid @enderror"
                                        id="alamat" name="alamat" value="{{ old('alamat') }}" required>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                 <div class="col-md-12 mb-3">
                                    <label for="kendaraan" class="form-label">RT</label>
                                    <input type="text" class="form-control @error('alamat') is-invalid @enderror"
                                        id="rt" name="rt" value="{{ old('rt') }}" required>
                                    @error('rt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                 <div class="col-md-12 mb-3">
                                    <label for="kendaraan" class="form-label">RT</label>
                                    <input type="text" class="form-control @error('rw') is-invalid @enderror"
                                        id="rw" name="rw" value="{{ old('rw') }}" required>
                                    @error('rw')
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
