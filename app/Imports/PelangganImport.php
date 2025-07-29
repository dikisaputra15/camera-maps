<?php

namespace App\Imports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PelangganImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
          return Pelanggan::updateOrCreate(
            [
                'id_pel' => $row['id_pel']
            ],
            [
                'no_meter' => $row['no_meter'],
                'nama' => $row['nama'],
                'tarif' => $row['tarif'],
                'daya' => $row['daya'],
                'jenis_layanan' => $row['jenis_layanan'],
                'alamat' => $row['alamat'],
                'rt' => $row['rt'],
                'rw' => $row['rw'],
            ]
        );
    }
}
