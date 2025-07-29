<?php

namespace App\Exports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\Storage;

class PelangganExport implements FromCollection, WithHeadings, WithMapping, WithDrawings, ShouldAutoSize
{
    protected $data;

    public function __construct()
    {
        $this->data = Pelanggan::all();
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID Pelanggan',
            'No Meter',
            'Nama',
            'Tarif',
            'Daya',
            'Jenis Layanan',
            'Alamat',
            'RT',
            'RW',
            'Foto KWH',
            'Foto Rumah',
            'Foto SR',
            'Foto Tiang',
            'Lat KWH',
            'Lon KWH',
            'Difoto Oleh',
            'Tanggal Foto',
            'Hasil Kunjungan',
            'Telepon',
            'Kabel SL',
            'Jenis Sambungan',
            'Merk MCB',
            'Ampere MCB',
            'Gardu',
            'Verified',
        ];
    }

    public function map($pelanggan): array
    {
        return [
            $pelanggan->id_pel,
            $pelanggan->no_meter,
            $pelanggan->nama,
            $pelanggan->tarif,
            $pelanggan->daya,
            $pelanggan->jenis_layanan,
            $pelanggan->alamat,
            $pelanggan->rt,
            $pelanggan->rw,
            '', // Gambar KWH
            '', // Gambar Rumah
            '', // Gambar SR
            '', // Gambar Tiang
            $pelanggan->kwh_latitude,
            $pelanggan->kwh_longitude,
            $pelanggan->difoto_oleh,
            $pelanggan->tanggal_foto ? $pelanggan->tanggal_foto->format('Y-m-d H:i:s') : '',
            $pelanggan->hasil_kunjungan,
            $pelanggan->telp,
            $pelanggan->kabel_sl,
            $pelanggan->jenis_sambungan,
            $pelanggan->merk_mcb,
            $pelanggan->ampere_mcb,
            $pelanggan->gardu,
            $pelanggan->verified ? 'Ya' : 'Tidak',
        ];
    }

    public function drawings()
    {
        $drawings = [];
        $row = 2; // baris dimulai setelah heading

        foreach ($this->data as $pelanggan) {
            // Utility: fungsi pembuat gambar
            $makeDrawing = function ($imagePath, $cell, $title) use ($row) {
                if (Storage::disk('public')->exists($imagePath)) {
                    $drawing = new Drawing();
                    $drawing->setName($title);
                    $drawing->setDescription($title);
                    $drawing->setPath(Storage::disk('public')->path($imagePath));
                    $drawing->setHeight(80);
                    $drawing->setCoordinates($cell . $row);
                    return $drawing;
                }
                return null;
            };

            // Gambar KWH (kolom J)
            if ($drawing = $makeDrawing($pelanggan->gambar_kwh, 'J', 'Foto KWH')) {
                $drawings[] = $drawing;
            }

            // Gambar Rumah (kolom K)
            if ($drawing = $makeDrawing($pelanggan->gambar_rumah, 'K', 'Foto Rumah')) {
                $drawings[] = $drawing;
            }

            // Gambar SR (kolom L)
            if ($drawing = $makeDrawing($pelanggan->gambar_sr, 'L', 'Foto SR')) {
                $drawings[] = $drawing;
            }

            // Gambar Tiang (kolom M)
            if ($drawing = $makeDrawing($pelanggan->gambar_tiang, 'M', 'Foto Tiang')) {
                $drawings[] = $drawing;
            }

            $row++;
        }

        return $drawings;
    }
}
