<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pelanggan::all();

          return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<a href="' . route('pelanggan.edit', $row->id) . '" class="btn btn-primary btn-sm">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'">Delete</button>';
            })
            ->rawColumns(['action'])
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
        $id_pel   = trim($request->id_pel);
        $no_meter = trim($request->no_meter);

        if ($id_pel === '' && $no_meter === '') {
            return response()->json([
                'success' => false,
                'html'    => '<p class="text-danger">Masukkan ID Pel atau No. Meter lebih dulu.</p>'
            ]);
        }

        $data = DB::table('pelanggans')
            ->when($id_pel && $no_meter, function ($query) use ($id_pel, $no_meter) {
                // Jika keduanya diisi, gunakan OR
                $query->where(function ($q) use ($id_pel, $no_meter) {
                    $q->where('id_pel', $id_pel)
                    ->orWhere('no_meter', $no_meter);
                });
            })
            ->when($id_pel && !$no_meter, function ($query) use ($id_pel) {
                $query->where('id_pel', $id_pel);
            })
            ->when($no_meter && !$id_pel, function ($query) use ($no_meter) {
                $query->where('no_meter', $no_meter);
            })
            /*
            ->where(function ($query) {
                $query->whereNull('gambar_kwh')
                    ->orWhere('gambar_kwh', '')
                    ->orWhereNull('gambar_rumah')
                    ->orWhere('gambar_rumah', '');
            })
            */
            ->get();

        if ($data->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'html'    => view('pelanggan.result-pelanggan', compact('data'))->render()
            ]);
        }

        return response()->json([
            'success' => false,
            'html'    => '<p class="text-danger">Data pelanggan tidak ditemukan.</p>'
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

        // Validasi, pastikan gambar yang dikirim adalah string base64 yang valid atau penanda 'EXISTS_AND_UNCHANGED'
        // Jika Anda menggunakan logika 'EXISTS_AND_UNCHANGED' dari frontend, validasi ini perlu disesuaikan.
        // Untuk saat ini, asumsikan 'required' hanya jika ada pengiriman gambar baru.
        // Atau ubah 'required' menjadi 'nullable' jika memungkinkan tidak mengirim gambar.
        $request->validate([
            'gambar_kwh'   => 'nullable|string', // Bisa null jika tidak ada perubahan
            'gambar_rumah' => 'nullable|string', // Bisa null jika tidak ada perubahan
        ]);

        try {
            // Fungsi menyimpan base64 ke storage
            $storeBase64Image = function (string $base64Image, string $folder, string $prefix) {
                // Bersihkan header base64
                $base64 = preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $base64Image);
                $base64 = str_replace(' ', '+', $base64);

                // Nama file unik
                $filename = $prefix . '_' . Str::uuid() . '.png';

                // Simpan ke file sementara
                $tempPath = sys_get_temp_dir() . '/' . $filename;
                file_put_contents($tempPath, base64_decode($base64));

                // Gunakan storeAs
                // Pastikan folder 'pelanggan' ada di disk 'public'
                $storedPath = Storage::disk('public')
                    ->putFileAs("pelanggan/{$folder}", new \Illuminate\Http\File($tempPath), $filename);

                // Hapus file temp
                unlink($tempPath);

                return $storedPath; // Hanya path relatif dari 'public'
            };

            // --- Logika untuk Gambar KWH ---
            // Cek apakah ada gambar KWH baru yang dikirim (bukan 'EXISTS_AND_UNCHANGED' atau null)
            if ($request->has('gambar_kwh') && $request->gambar_kwh !== 'EXISTS_AND_UNCHANGED' && $request->gambar_kwh !== 'null') {
                // Hapus gambar KWH lama jika ada
                if ($pelanggan->gambar_kwh && Storage::disk('public')->exists($pelanggan->gambar_kwh)) {
                    Storage::disk('public')->delete($pelanggan->gambar_kwh);
                    \Log::info("Gambar KWH lama dihapus: {$pelanggan->gambar_kwh}");
                }

                // Simpan gambar KWH baru
                $gambarKWHPath = $storeBase64Image($request->gambar_kwh, 'kwh', 'kwh');
                $pelanggan->gambar_kwh = $gambarKWHPath;
                \Log::info("Gambar KWH baru disimpan: {$gambarKWHPath}");

            } elseif ($request->gambar_kwh === 'null') {
                // Jika frontend secara eksplisit mengirim 'null', hapus gambar lama dan set path ke null
                if ($pelanggan->gambar_kwh && Storage::disk('public')->exists($pelanggan->gambar_kwh)) {
                    Storage::disk('public')->delete($pelanggan->gambar_kwh);
                    \Log::info("Gambar KWH lama dihapus karena null: {$pelanggan->gambar_kwh}");
                }
                $pelanggan->gambar_kwh = null;
            }


            // --- Logika untuk Gambar Rumah ---
            // Cek apakah ada gambar Rumah baru yang dikirim (bukan 'EXISTS_AND_UNCHANGED' atau null)
            if ($request->has('gambar_rumah') && $request->gambar_rumah !== 'EXISTS_AND_UNCHANGED' && $request->gambar_rumah !== 'null') {
                // Hapus gambar Rumah lama jika ada
                if ($pelanggan->gambar_rumah && Storage::disk('public')->exists($pelanggan->gambar_rumah)) {
                    Storage::disk('public')->delete($pelanggan->gambar_rumah);
                    \Log::info("Gambar Rumah lama dihapus: {$pelanggan->gambar_rumah}");
                }

                // Simpan gambar Rumah baru
                $gambarRumahPath = $storeBase64Image($request->gambar_rumah, 'rumah', 'rumah');
                $pelanggan->gambar_rumah = $gambarRumahPath;
                \Log::info("Gambar Rumah baru disimpan: {$gambarRumahPath}");

            } elseif ($request->gambar_rumah === 'null') {
                // Jika frontend secara eksplisit mengirim 'null', hapus gambar lama dan set path ke null
                if ($pelanggan->gambar_rumah && Storage::disk('public')->exists($pelanggan->gambar_rumah)) {
                    Storage::disk('public')->delete($pelanggan->gambar_rumah);
                    \Log::info("Gambar Rumah lama dihapus karena null: {$pelanggan->gambar_rumah}");
                }
                $pelanggan->gambar_rumah = null;
            }

            $pelanggan->save();

            return response()->json(['message' => 'Gambar berhasil diupdate'], 200);

        } catch (\Throwable $e) {
            \Log::error("Gagal update gambar pelanggan {$id}: {$e->getMessage()}");
            return response()->json(['message' => 'Gagal mengupdate gambar.'], 500);
        }
    }
}
