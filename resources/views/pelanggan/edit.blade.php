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
                            <label>Difoto Oleh</label>
                            <input type="text" name="difoto_oleh" value="{{ old('difoto_oleh', $pelanggan->difoto_oleh) }}" class="form-control" required>
                        </div>

                         <div class="form-group">
                            <label>Tanggal Foto</label>
                           <input type="datetime-local" name="tanggal_foto"
                            value="{{ old('tanggal_foto', $pelanggan->tanggal_foto ? \Carbon\Carbon::parse($pelanggan->tanggal_foto)->format('Y-m-d\TH:i') : '') }}"
                            class="form-control" required>
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
                                <label for="jenis_sambungan" class="form-label">Jenis Layanan</label>
                            <select class="form-control" name="jenis_layanan" id="jenis_layanan" required>
                                    @php
                                        $layananOptions = [
                                            'Prabayar',
                                            'Pascabayar',
                                        ];
                                        $selectedLayanan = old('jenis_layanan', $pelanggan->jenis_layanan ?? '');
                                    @endphp

                                    @foreach ($layananOptions as $lay)
                                        <option value="{{ $lay }}" {{ $selectedLayanan === $lay ? 'selected' : '' }}>
                                            {{ $lay }}
                                        </option>
                                    @endforeach
                                </select>
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

                        <div class="form-group">
                            <label>Hasil Kunjungan</label>
                            <input type="text" name="hasil_kunjungan" value="{{ old('hasil_kunjungan', $pelanggan->hasil_kunjungan) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Telephone</label>
                            <input type="text" name="telp" value="{{ old('telp', $pelanggan->telp) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="kabel_sl" class="form-label">Kabel SL</label>
                        <select class="form-control" name="kabel_sl" id="kabel_sl" required>
                                @php
                                    $kabelOptions = [
                                        'TIC 2x10 mm2',
                                        'TIC 2x16 mm2',
                                        'TIC 4x16 mm2',
                                        'TIC 4x25 mm2',
                                        'TIC 4x75 mm2',
                                        'SKSR',
                                    ];
                                    $selectedKabel = old('kabel_sl', $pelanggan->kabel_sl ?? '');
                                @endphp

                                @foreach ($kabelOptions as $option)
                                    <option value="{{ $option }}" {{ $selectedKabel === $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                         <div class="form-group">
                        <label for="jenis_sambungan" class="form-label">Jenis Sambungan</label>
                    <select class="form-control" name="jenis_sambungan" id="jenis_sambungan" required>
                            @php
                                $sambungOptions = [
                                    'Langsung',
                                    'Seri',
                                ];
                                $selectedSambung = old('jenis_sambungan', $pelanggan->jenis_sambungan ?? '');
                            @endphp

                            @foreach ($sambungOptions as $sam)
                                <option value="{{ $sam }}" {{ $selectedSambung === $sam ? 'selected' : '' }}>
                                    {{ $sam }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                     <div class="form-group">
                        <label for="merk_mcb" class="form-label">Merk MCB</label>
                        <input type="text" class="form-control" id="merk_mcb" name="merk_mcb" value="{{ $pelanggan ? $pelanggan->merk_mcb : '' }}">
                    </div>

                      <div class="form-group">
                <label for="ampere_mcb" class="form-label">Ampere MCB</label>
               <select class="form-control" name="ampere_mcb" id="ampere_mcb" required>
                    @php
                        $ampereOptions = [
                            '1ph 2A',
                            '1ph 4A',
                            '1ph 6A',
                            '1ph 10A',
                            '1ph 16A',
                            '1ph 20A',
                            '1ph 25A',
                            '1ph 35A',
                            '1ph 50A',
                            '3ph 10A',
                            '3ph 16A',
                            '3ph 20A',
                            '3ph 25A',
                            '3ph 35A',
                            '3ph 50A',
                        ];
                        $selectedAmpere = old('ampere_mcb', $pelanggan->ampere_mcb ?? '');
                    @endphp

                    @foreach ($ampereOptions as $amp)
                        <option value="{{ $amp }}" {{ $selectedAmpere === $amp ? 'selected' : '' }}>
                            {{ $amp }}
                        </option>
                    @endforeach
                </select>
            </div>

             <div class="form-group">
                <label for="gardu" class="form-label">Gardu</label>
                <input type="text" class="form-control" id="gardu" name="gardu" value="{{ $pelanggan ? $pelanggan->gardu : '' }}">
            </div>

             <div class="form-group">
                    <label for="jenis_sambungan" class="form-label">Gambar KWH</label></br>
                    <img src="{{ asset('storage/' . $pelanggan->gambar_kwh) }}" style="width:250px; height:250px;" />
             </div>

              <div class="form-group">
                    <label for="jenis_sambungan" class="form-label">Gambar Rumah</label></br>
                    <img src="{{ asset('storage/' . $pelanggan->gambar_rumah) }}" style="width:250px; height:250px;" />
             </div>

              <div class="form-group">
                    <label for="jenis_sambungan" class="form-label">Gambar SR</label></br>
                    <img src="{{ asset('storage/' . $pelanggan->gambar_sr) }}" style="width:250px; height:250px;" />
             </div>

              <div class="form-group">
                    <label for="jenis_sambungan" class="form-label">Gambar Tiang</label></br>
                    <img src="{{ asset('storage/' . $pelanggan->gambar_tiang) }}" style="width:250px; height:250px;" />
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
