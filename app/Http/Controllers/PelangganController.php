<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Imports\PelangganImport;
use App\Exports\PelangganExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pelanggan::orderBy('id', 'desc')->get();

          return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('verified', function ($row) {
                return (bool) $row->verified;
            })
             ->addColumn('gambar_kwh', function ($row) {
                    // Check if gambar_kwh exists and create an <img> tag
                    if ($row->gambar_kwh) {
                        // Use Storage::url() to get the public URL for the stored image
                        // Assumes images are stored in storage/app/public/pelanggan/kwh/
                        $imageUrl = Storage::url($row->gambar_kwh);
                        return '<img src="' . $imageUrl . '" alt="Gambar KWH" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">';
                    }
                    return 'Tidak ada gambar'; // Or an empty string, or a placeholder image
                })
            ->addColumn('gambar_rumah', function ($row) {
                    // Check if gambar_rumah exists and create an <img> tag
                    if ($row->gambar_rumah) {
                        $imageUrl = Storage::url($row->gambar_rumah);
                        return '<img src="' . $imageUrl . '" alt="Gambar Rumah" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">';
                    }
                    return 'Tidak ada gambar'; // Or an empty string, or a placeholder image
                })
            ->addColumn('gambar_sr', function ($row) {
                    // Check if gambar_rumah exists and create an <img> tag
                    if ($row->gambar_sr) {
                        $imageUrl = Storage::url($row->gambar_sr);
                        return '<img src="' . $imageUrl . '" alt="Gambar SR" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">';
                    }
                    return 'Tidak ada gambar'; // Or an empty string, or a placeholder image
                })
             ->addColumn('gambar_tiang', function ($row) {
                    // Check if gambar_rumah exists and create an <img> tag
                    if ($row->gambar_tiang) {
                        $imageUrl = Storage::url($row->gambar_tiang);
                        return '<img src="' . $imageUrl . '" alt="Gambar SR" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">';
                    }
                    return 'Tidak ada gambar'; // Or an empty string, or a placeholder image
                })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('pelanggan.edit', $row->id) . '" class="btn btn-primary btn-sm">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'">Delete</button>';
            })
            ->rawColumns(['verified','gambar_kwh','gambar_rumah','gambar_sr','gambar_tiang','action'])
            ->make(true);

        }
        return view('pelanggan.index', [
                'title' => "Pelanggan",
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('pelanggan.create', [
            'title' => "Create Pelanggan"
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Pelanggan::create([
            'id_pel' => $request->id_pel,
            'no_meter' => $request->no_meter,
            'nama' => $request->nama,
            'tarif' => $request->tarif,
            'daya' => $request->daya,
            'jenis_layanan' => $request->jenis_layanan,
            'alamat' => $request->alamat,
            'rt' => $request->rt,
            'rw' => $request->rw,
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'id_pel' => 'required|string|max:255',
            'no_meter' => 'required|string|max:255',
        ]);

        $pelanggan->id_pel = $request->id_pel;
        $pelanggan->no_meter = $request->no_meter;
        $pelanggan->difoto_oleh = $request->difoto_oleh;
        $pelanggan->tanggal_foto = $request->tanggal_foto;
        $pelanggan->nama = $request->nama;
        $pelanggan->tarif = $request->tarif;
        $pelanggan->daya = $request->daya;
        $pelanggan->jenis_layanan = $request->jenis_layanan;
        $pelanggan->alamat = $request->alamat;
        $pelanggan->rt = $request->rt;
        $pelanggan->rw = $request->rw;
        $pelanggan->hasil_kunjungan = $request->hasil_kunjungan;
        $pelanggan->telp = $request->telp;
        $pelanggan->kabel_sl = $request->kabel_sl;
        $pelanggan->jenis_sambungan = $request->jenis_sambungan;
        $pelanggan->merk_mcb = $request->merk_mcb;
        $pelanggan->ampere_mcb = $request->ampere_mcb;
        $pelanggan->gardu = $request->gardu;
        $pelanggan->verified = $request->has('verified');

        $pelanggan->save();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $reqtype = Pelanggan::findOrFail($id);

        if($reqtype->delete()){
            $response['success'] = 1;
            $response['msg'] = 'Delete successfully';
        }else{
            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }

        return response()->json($response);
    }

     public function formsearch()
    {
        return view('pelanggan.formsearch', [
            'title' => "Search Pelanggan"
        ]);
    }

    public function searchPelanggan(Request $request)
    {
        // Validasi input
        $request->validate([
            'search_query' => 'required|string|max:255',
        ]);

        $query = $request->input('search_query');

        // Lakukan pencarian berdasarkan id_pel ATAU no_meter
        $data = Pelanggan::where('id_pel', $query)
                              ->orWhere('no_meter', $query)
                              ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan.'
            ]);
        }

         $html = view('pelanggan.result-pelanggan', compact('data'))->render();

        return response()->json([
            'success' => true,
            'html'    => $html
        ]);

    }

     public function formupload($id)
    {
        $pelanggan = Pelanggan::find($id);

        return view('pelanggan.formupload', compact('pelanggan'));
    }

   public function updateImages(Request $request, $id)
   {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
        }

        // Define a reusable function to handle saving base64 images
        $storeBase64Image = function (string $base64Image, string $folder, string $prefix, $existingPath = null) {
            // Check if the image data is valid and not a placeholder
            if ($base64Image && $base64Image !== 'EXISTS_AND_UNCHANGED' && $base64Image !== 'null') {
                // Decode base64 string
                $base64 = preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $base64Image);
                $base64 = str_replace(' ', '+', $base64);

                // Determine image type (png, jpeg, etc.) - assuming png for simplicity or extract from data URI
                $type = 'png';
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
                    $type = strtolower($matches[1]);
                }

                $filename = $prefix . '_' . Str::uuid() . '.' . $type;
                $path = "pelanggan/{$folder}/{$filename}"; // Define full path in storage

                // Store the image
                Storage::disk('public')->put($path, base64_decode($base64));

                // Delete old image if it exists and is different from the new one
                if ($existingPath && Storage::disk('public')->exists($existingPath) && $existingPath !== $path) {
                    Storage::disk('public')->delete($existingPath);
                }

                return $path; // Return the path relative to the public disk
            } elseif ($base64Image === 'null') {
                // If 'null' is explicitly sent, delete existing image and set to null
                if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                    Storage::disk('public')->delete($existingPath);
                }
                return null;
            }
            // If 'EXISTS_AND_UNCHANGED' or no new image, return existing path
            return $existingPath;
        };

        try {
            // Validate all incoming data, including new fields and coordinates
            $validatedData = $request->validate([
                'gambar_kwh'        => 'nullable|string',
                'kwh_latitude'      => 'nullable|numeric', // Only KWH latitude/longitude validated
                'kwh_longitude'     => 'nullable|numeric', // Only KWH latitude/longitude validated
                'gambar_rumah'      => 'nullable|string',
                // Removed validation for rumah_latitude, rumah_longitude
                'gambar_sr'         => 'nullable|string',
                // Removed validation for sr_latitude, sr_longitude
                'gambar_tiang'      => 'nullable|string',
                // Removed validation for tiang_latitude, tiang_longitude
                'hasil_kunjungan'   => 'nullable|string',
                'telp'              => 'nullable|string',
                'kabel_sl'          => 'nullable|string',
                'jenis_sambungan'   => 'nullable|string',
                'merk_mcb'          => 'nullable|string',
                'ampere_mcb'        => 'nullable|string',
                'gardu'             => 'nullable|string',
            ]);

            // --- Update Gambar KWH ---
            $pelanggan->gambar_kwh = $storeBase64Image(
                $validatedData['gambar_kwh'] ?? null,
                'kwh',
                'kwh',
                $pelanggan->gambar_kwh
            );
            $pelanggan->kwh_latitude = $validatedData['kwh_latitude'] ?? null;
            $pelanggan->kwh_longitude = $validatedData['kwh_longitude'] ?? null;

            // --- Update Gambar Rumah ---
            $pelanggan->gambar_rumah = $storeBase64Image(
                $validatedData['gambar_rumah'] ?? null,
                'rumah',
                'rumah',
                $pelanggan->gambar_rumah
            );
            // Removed assignment for rumah_latitude, rumah_longitude

            // --- Update Gambar SR ---
            $pelanggan->gambar_sr = $storeBase64Image(
                $validatedData['gambar_sr'] ?? null,
                'sr',
                'sr',
                $pelanggan->gambar_sr
            );
            // Removed assignment for sr_latitude, sr_longitude

            // --- Update Gambar Tiang ---
            $pelanggan->gambar_tiang = $storeBase64Image(
                $validatedData['gambar_tiang'] ?? null,
                'tiang',
                'tiang',
                $pelanggan->gambar_tiang
            );
            // Removed assignment for tiang_latitude, tiang_longitude

            // --- Update other form fields ---
            $pelanggan->hasil_kunjungan = $validatedData['hasil_kunjungan'] ?? $pelanggan->hasil_kunjungan;
            $pelanggan->telp = $validatedData['telp'] ?? $pelanggan->telp;
            $pelanggan->kabel_sl = $validatedData['kabel_sl'] ?? $pelanggan->kabel_sl;
            $pelanggan->jenis_sambungan = $validatedData['jenis_sambungan'] ?? $pelanggan->jenis_sambungan;
            $pelanggan->merk_mcb = $validatedData['merk_mcb'] ?? $pelanggan->merk_mcb;
            $pelanggan->ampere_mcb = $validatedData['ampere_mcb'] ?? $pelanggan->ampere_mcb;
            $pelanggan->gardu = $validatedData['gardu'] ?? $pelanggan->gardu;

            // --- Auto-filled/system fields ---
            $pelanggan->difoto_oleh = auth()->check() ? auth()->user()->name : 'Guest';
            $pelanggan->tanggal_foto = now('Asia/Jakarta');
            $pelanggan->verified = false; // Assuming it resets to false on any update

            $pelanggan->save();

            return response()->json(['message' => 'Gambar dan data pelanggan berhasil diperbarui!'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation Error for pelanggan {$id}: " . json_encode($e->errors()));
            return response()->json(['message' => 'Data tidak valid.', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error("Gagal update gambar atau data pelanggan {$id}: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
            return response()->json(['message' => 'Gagal mengupdate gambar dan data.'], 500);
        }
    }

  public function updateVerified(Request $request, $id)
{
    $pelanggan = Pelanggan::find($id);

    if (!$pelanggan) {
        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
    }

    $pelanggan->verified = $request->verified ? true : false;

    if ($pelanggan->save()) {
        return response()->json(['success' => true, 'message' => 'Status verifikasi berhasil diperbarui.']);
    }

    return response()->json(['success' => false, 'message' => 'Gagal memperbarui status verifikasi.'], 500);
}

  public function import(Request $request)
    {
        Excel::import(new PelangganImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data berhasil diimport dan diperbarui!');
    }

    public function exportExcel()
    {
        // Nama file yang akan diunduh oleh pengguna
        $fileName = 'data_pelanggan_' . date('Ymd_His') . '.xlsx';

        // Unduh file Excel
        return Excel::download(new PelangganExport, $fileName);
    }

}
