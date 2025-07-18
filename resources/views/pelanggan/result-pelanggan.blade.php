<div class="section-body">
                <div class="card">
                    <div class="card-body">
<table class="table-striped table">
    <thead>
        <tr>
            <th>ID Pelanggan</th>
            <th>No Meter</th>
            <th>Gambar KWH</th>
            <th>Gambar Rumah</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row->id_pel }}</td>
            <td>{{ $row->no_meter }}</td>
            <td>
                @if(empty($row->gambar_kwh))
                    <span class="badge bg-danger">Belum Ada</span>
                @else
                    <a href="{{ asset('storage/' . $row->gambar_kwh) }}" target="_blank">Lihat</a>
                @endif
            </td>
            <td>
                @if(empty($row->gambar_rumah))
                    <span class="badge bg-danger">Belum Ada</span>
                @else
                    <a href="{{ asset('storage/' . $row->gambar_rumah) }}" target="_blank">Lihat</a>
                @endif
            </td>
            <td>
                 @if(empty($row->gambar_kwh) && empty($row->gambar_rumah))
                        <a href="search-pelanggan/{{$row->id}}/formupload" class="btn btn-primary">Upload</a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
</div>
</div>
