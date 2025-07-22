<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            ->addColumn('action', function ($row) {
                return '<a href="' . route('pelanggan.edit', $row->id) . '" class="btn btn-primary btn-sm">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'">Delete</button>';
            })
            ->rawColumns(['verified','gambar_kwh','gambar_rumah','action'])
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

        $request->validate([
            'gambar_kwh'     => 'nullable|string',
            'gambar_rumah'   => 'nullable|string',
            'difoto_oleh'    => 'nullable|string',
            'tanggal_foto'   => 'nullable|date',
            'kwh_latitude'   => 'nullable|numeric',
            'kwh_longitude'  => 'nullable|numeric',
        ]);

        try {
            $storeBase64Image = function (string $base64Image, string $folder, string $prefix) {
                $base64 = preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $base64Image);
                $base64 = str_replace(' ', '+', $base64);
                $filename = $prefix . '_' . Str::uuid() . '.png';
                $tempPath = sys_get_temp_dir() . '/' . $filename;
                file_put_contents($tempPath, base64_decode($base64));
                $storedPath = Storage::disk('public')->putFileAs("pelanggan/{$folder}", new \Illuminate\Http\File($tempPath), $filename);
                unlink($tempPath);
                return $storedPath;
            };

            // ---------- Gambar KWH ----------
            if ($request->has('gambar_kwh') && $request->gambar_kwh !== 'EXISTS_AND_UNCHANGED' && $request->gambar_kwh !== 'null') {
                if ($pelanggan->gambar_kwh && Storage::disk('public')->exists($pelanggan->gambar_kwh)) {
                    Storage::disk('public')->delete($pelanggan->gambar_kwh);
                }
                $pelanggan->gambar_kwh = $storeBase64Image($request->gambar_kwh, 'kwh', 'kwh');

            } elseif ($request->gambar_kwh === 'null') {
                if ($pelanggan->gambar_kwh && Storage::disk('public')->exists($pelanggan->gambar_kwh)) {
                    Storage::disk('public')->delete($pelanggan->gambar_kwh);
                }
                $pelanggan->gambar_kwh = null;
            }

            // ---------- Gambar Rumah ----------
            if ($request->has('gambar_rumah') && $request->gambar_rumah !== 'EXISTS_AND_UNCHANGED' && $request->gambar_rumah !== 'null') {
                if ($pelanggan->gambar_rumah && Storage::disk('public')->exists($pelanggan->gambar_rumah)) {
                    Storage::disk('public')->delete($pelanggan->gambar_rumah);
                }
                $pelanggan->gambar_rumah = $storeBase64Image($request->gambar_rumah, 'rumah', 'rumah');
            } elseif ($request->gambar_rumah === 'null') {
                if ($pelanggan->gambar_rumah && Storage::disk('public')->exists($pelanggan->gambar_rumah)) {
                    Storage::disk('public')->delete($pelanggan->gambar_rumah);
                }
                $pelanggan->gambar_rumah = null;
            }

            // ---------- Lokasi ----------
            if ($request->kwh_latitude && $request->kwh_longitude) {
                $pelanggan->kwh_latitude = $request->kwh_latitude;
                $pelanggan->kwh_longitude = $request->kwh_longitude;
            }


            $pelanggan->difoto_oleh = auth()->user()->name;
            $pelanggan->tanggal_foto = now('Asia/Jakarta');
            $pelanggan->verified = false;
            $pelanggan->save();

            return response()->json(['message' => 'Gambar berhasil diupdate'], 200);

        } catch (\Throwable $e) {
            \Log::error("Gagal update gambar pelanggan {$id}: {$e->getMessage()}");
            return response()->json(['message' => 'Gagal mengupdate gambar.'], 500);
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


}
