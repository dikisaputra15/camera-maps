<?php

namespace App\Exports;

use App\Models\Pelanggan;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles; // Tetap gunakan ini
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // Tambahkan ini

class PelangganExport implements FromCollection, WithHeadings, WithMapping, WithDrawings, ShouldAutoSize, WithStyles // Hapus WithCustomRowHeight
{
    protected $data;
    private $imageHeight = 80; // Tentukan tinggi gambar yang konsisten dalam piksel

    public function __construct()
    {
        $this->data = Pelanggan::select(
            'id_pel', 'no_meter', 'difoto_oleh', 'tanggal_foto', 'kwh_latitude', 'kwh_longitude',
            'nama', 'tarif', 'daya', 'jenis_layanan', 'alamat', 'rt', 'rw', 'hasil_kunjungan',
            'telp', 'kabel_sl', 'jenis_sambungan', 'merk_mcb', 'ampere_mcb', 'gardu', 'verified',
            'gambar_kwh', 'gambar_rumah', 'gambar_sr', 'gambar_tiang'
        )->get();
    }

    public function collection()
    {
        return $this->data;
    }

    public function map($row): array
    {
        return [
            $row->id_pel,
            $row->no_meter,
            $row->difoto_oleh,
            $row->tanggal_foto,
            $row->kwh_latitude,
            $row->kwh_longitude,
            $row->nama,
            $row->tarif,
            $row->daya,
            $row->jenis_layanan,
            $row->alamat,
            $row->rt,
            $row->rw,
            $row->hasil_kunjungan,
            $row->telp,
            $row->kabel_sl,
            $row->jenis_sambungan,
            $row->merk_mcb,
            $row->ampere_mcb,
            $row->gardu,
            $row->verified ? 'Ya' : 'Tidak',
            '', // Placeholder for Gambar KWH
            '', // Placeholder for Gambar Rumah
            '', // Placeholder for Gambar SR
            '', // Placeholder for Gambar Tiang
        ];
    }

    public function headings(): array
    {
        return [
            'ID Pelanggan', 'No Meter', 'Difoto Oleh', 'Tanggal Foto', 'Kwh Latitude', 'Kwh Longitude',
            'Nama', 'Tarif', 'Daya', 'Jenis Layanan', 'Alamat', 'RT', 'RW', 'Hasil Kunjungan',
            'Telepon', 'Kabel SL', 'Jenis Sambungan', 'Merk MCB', 'Ampere MCB', 'Gardu', 'Verified',
            'Gambar KWH', 'Gambar Rumah', 'Gambar SR', 'Gambar Tiang',
        ];
    }

    /**
     * Apply styles to the worksheet (e.g., row height and column width)
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Set a consistent row height for all data rows to accommodate the images
        // Loop through all data rows (starting from row 2 because row 1 is header)
        for ($i = 2; $i <= $this->data->count() + 1; $i++) {
            // Tinggi baris diukur dalam poin. Kira-kira 1 piksel = 0.75 poin.
            // Jadi, jika gambar Anda 80px, maka 80 * 0.75 = 60 poin adalah tinggi baris yang baik.
            $sheet->getRowDimension($i)->setRowHeight(60); // Sesuaikan nilai ini
        }

        // Optional: Set column widths for image columns if ShouldAutoSize makes them too wide or too narrow.
        // Uncomment baris di bawah ini jika Anda ingin mengatur lebar kolom secara manual untuk kolom gambar.
        // Jika Anda ingin kolom-kolom lain tetap ShouldAutoSize, ini adalah cara yang baik.
        // Jika Anda ingin semua kolom diatur secara manual, Anda bisa menghapus ShouldAutoSize
        // dan mengimplementasikan WithColumnWidths.
        $sheet->getColumnDimension('V')->setWidth(15); // Lebar kolom untuk Gambar KWH
        $sheet->getColumnDimension('W')->setWidth(15); // Lebar kolom untuk Gambar Rumah
        $sheet->getColumnDimension('X')->setWidth(15); // Lebar kolom untuk Gambar SR
        $sheet->getColumnDimension('Y')->setWidth(15); // Lebar kolom untuk Gambar Tiang
    }


    public function drawings()
    {
        $drawings = [];

        foreach ($this->data as $index => $item) {
            $row = $index + 2; // +2 karena row 1 adalah header

            $gambarFields = [
                'gambar_kwh' => 'V',
                'gambar_rumah' => 'W',
                'gambar_sr' => 'X',
                'gambar_tiang' => 'Y',
            ];

            foreach ($gambarFields as $field => $column) {
                if ($item->{$field}) {
                    $fullPath = storage_path('app/public/' . $item->{$field});

                    if (file_exists($fullPath)) {
                        $drawing = new Drawing();
                        $drawing->setPath($fullPath);
                        $drawing->setCoordinates($column . $row);

                        // Set a consistent height for all images
                        $drawing->setHeight($this->imageHeight);

                        // Calculate width to maintain aspect ratio
                        list($width, $height) = getimagesize($fullPath);
                        if ($height > 0) {
                            $drawing->setWidth($this->imageHeight * ($width / $height));
                        } else {
                            // Fallback if height is 0 to avoid division by zero
                            $drawing->setWidth($this->imageHeight); // Or some default width
                        }

                        // Offset to center the image within the cell (adjust as needed)
                        // Penyesuaian ini akan membantu memposisikan gambar di dalam sel.
                        // Sesuaikan offset agar gambar terlihat rapi di tengah sel.
                        $drawing->setOffsetX(5); // Offset dari batas kiri sel
                        $drawing->setOffsetY(5); // Offset dari batas atas sel

                        $drawings[] = $drawing;
                    }
                }
            }
        }

        return $drawings;
    }
}
