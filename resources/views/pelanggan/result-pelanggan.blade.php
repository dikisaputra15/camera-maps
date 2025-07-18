
<table class="table-striped table">
    <thead>
        <tr>
            <th style="width: 60%;">ID Pelanggan</th>
            <th style="width: 40%;">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row->id_pel }}</td>
            <td>
                 @if(empty($row->gambar_kwh) && empty($row->gambar_rumah))
                        <a href="search-pelanggan/{{$row->id}}/formupload" class="btn btn-primary">Upload</a>
                    @else
                        <a href="search-pelanggan/{{$row->id}}/formupload" class="btn btn-warning btn-sm">Reupload</a>
                    @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
