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

                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama" value="{{ old('nama', $pelanggan->nama) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Tarif</label>
                            <input type="text" name="tarif" value="{{ old('tarif', $pelanggan->tarif) }}" class="form-control" required>
                        </div>

                         <div class="form-group">
                            <label>Daya</label>
                            <input type="text" name="daya" value="{{ old('daya', $pelanggan->daya) }}" class="form-control" required>
                        </div>

                         <div class="form-group">
                            <label>Jenis Layanan</label>
                            <input type="text" name="jenis_layanan" value="{{ old('jenis_layanan', $pelanggan->jenis_layanan) }}" class="form-control" required>
                        </div>

                         <div class="form-group">
                            <label>Alamat</label>
                            <input type="text" name="alamat" value="{{ old('alamat', $pelanggan->alamat) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>RT</label>
                            <input type="text" name="rt" value="{{ old('rt', $pelanggan->rt) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>RW</label>
                            <input type="text" name="rw" value="{{ old('rw', $pelanggan->rw) }}" class="form-control" required>
                        </div>

                          <div class="form-group form-check">
                                <input type="checkbox" name="verified" id="verified" class="form-check-input" value="{{ old('verified', $pelanggan->verified) }}"
                                    {{ old('verified', $pelanggan->verified) ? 'checked' : '' }}>
                                <label class="form-check-label" for="verified">Verified</label>
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
