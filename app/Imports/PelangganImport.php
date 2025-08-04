<?php

namespace App\Imports;

use App\Models\Pelanggan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnError; // Import SkipsOnError
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Import SkipsOnFailure
use Maatwebsite\Excel\Validators\Failure; // Import Failure class
use Throwable; // Import Throwable for general exceptions
use Illuminate\Support\Facades\Log; // Import Log facade

class PelangganImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, ShouldQueue, SkipsOnError, SkipsOnFailure
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Pastikan kolom 'id_pel' ada dan tidak kosong sebelum digunakan sebagai kunci
        // Gunakan operator null coalescing (??) untuk memberikan nilai default jika kunci tidak ada
        // atau jika nilainya null.
        $idPel = $row['id_pel'] ?? null;

        // Jika id_pel tidak ada atau kosong, kita bisa memilih untuk melewatkan baris ini
        // atau menghasilkan error. Untuk saat ini, kita akan melewatkannya.
        if (empty($idPel)) {
            Log::warning('Skipping row due to missing or empty id_pel', ['row_data' => $row]);
            return null; // Melewatkan baris ini
        }

        try {
            return Pelanggan::updateOrCreate(
                [
                    'id_pel' => (string) $idPel // Pastikan id_pel adalah string
                ],
                [
                    // Gunakan operator null coalescing untuk menghindari 'Undefined array key'
                    // jika salah satu kolom tidak ada di file Excel.
                    // Lakukan casting eksplisit untuk memastikan tipe data sesuai dengan kolom database.
                    'no_meter' => (string) ($row['no_meter'] ?? ''),
                    'nama' => (string) ($row['nama'] ?? ''),
                    'tarif' => (string) ($row['tarif'] ?? ''), // Sesuaikan tipe data jika ini int/float
                    'daya' => (int) ($row['daya'] ?? 0), // Pastikan ini integer
                    'jenis_layanan' => (string) ($row['jenis_layanan'] ?? ''),
                    'alamat' => (string) ($row['alamat'] ?? ''),
                    'rt' => (string) ($row['rt'] ?? ''), // Sesuaikan tipe data jika ini int
                    'rw' => (string) ($row['rw'] ?? ''), // Sesuaikan tipe data jika ini int
                ]
            );
        } catch (Throwable $e) {
            // Log error jika ada masalah saat menyimpan/memperbarui model
            Log::error('Error processing row for Pelanggan import: ' . $e->getMessage(), [
                'row_data' => $row,
                'error' => $e->getTraceAsString()
            ]);
            return null; // Melewatkan baris yang gagal
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @param Throwable $e
     */
    public function onError(Throwable $e)
    {
        // Implementasi opsional untuk menangani error pada tingkat baris
        // Misalnya, log error atau simpan ke tabel error khusus
        Log::error('Error during Pelanggan import process: ' . $e->getMessage(), [
            'exception' => $e->getTraceAsString()
        ]);
        // Anda bisa memilih untuk tidak melempar error di sini jika ingin proses berlanjut
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        // Implementasi opsional untuk menangani kegagalan validasi
        // Misalnya, log kegagalan atau simpan ke tabel error khusus
        foreach ($failures as $failure) {
            Log::warning('Validation failure during Pelanggan import:', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ]);
        }
    }
}
