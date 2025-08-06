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

        $user = Auth::user();

        if ($user->hasAnyRole(['admin', 'surveyor'])) {
            return view('pelanggan.formupload', compact('pelanggan'));
        } elseif ($user->hasRole('cater')) {
            return view('pelanggan.formupload_cater', compact('pelanggan'));
        } else {
            abort(403, 'Unauthorized access.');
        }

    }

  public function updateImages(Request $request, $id)
{
    $pelanggan = Pelanggan::find($id);

    if (!$pelanggan) {
        return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
    }

    try {
        $validated = $request->validate([
            'gambar_kwh'        => 'nullable|string',
            'kwh_latitude'      => 'nullable|numeric',
            'kwh_longitude'     => 'nullable|numeric',
            'gambar_rumah'      => 'nullable|string',
            'gambar_sr'         => 'nullable|string',
            'gambar_tiang'      => 'nullable|string',
            'hasil_kunjungan'   => 'nullable|string',
            'telp'              => 'nullable|string',
            'kabel_sl'          => 'nullable|string',
            'jenis_sambungan'   => 'nullable|string',
            'merk_mcb'          => 'nullable|string',
            'ampere_mcb'        => 'nullable|string',
            'gardu'             => 'nullable|string',
        ]);

        // Closure untuk menangani upload base64
        $handleBase64Image = function ($image, $folder, $prefix, $existingPath) {
            if (!$image || $image === 'EXISTS_AND_UNCHANGED') {
                return $existingPath;
            }

            if ($image === 'null') {
                if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                    Storage::disk('public')->delete($existingPath);
                }
                return null;
            }

            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $base64 = str_replace(' ', '+', $base64);

            if (empty($base64)) return $existingPath;

            $type = 'png';
            if (preg_match('/^data:image\/(\w+);base64,/', $image, $matches)) {
                $type = strtolower($matches[1]);
            }

            $filename = "{$prefix}_" . Str::uuid() . ".{$type}";
            $path = "pelanggan/{$folder}/{$filename}";

            Storage::disk('public')->put($path, base64_decode($base64));

            if ($existingPath && Storage::disk('public')->exists($existingPath) && $existingPath !== $path) {
                Storage::disk('public')->delete($existingPath);
            }

            return $path;
        };

        // Update gambar
        $pelanggan->gambar_kwh    = $handleBase64Image($validated['gambar_kwh'] ?? null, 'kwh', 'kwh', $pelanggan->gambar_kwh);
        $pelanggan->gambar_rumah  = $handleBase64Image($validated['gambar_rumah'] ?? null, 'rumah', 'rumah', $pelanggan->gambar_rumah);
        $pelanggan->gambar_sr     = $handleBase64Image($validated['gambar_sr'] ?? null, 'sr', 'sr', $pelanggan->gambar_sr);
        $pelanggan->gambar_tiang  = $handleBase64Image($validated['gambar_tiang'] ?? null, 'tiang', 'tiang', $pelanggan->gambar_tiang);

        // Update koordinat KWH
        $pelanggan->kwh_latitude  = $validated['kwh_latitude'] ?? null;
        $pelanggan->kwh_longitude = $validated['kwh_longitude'] ?? null;

        // Update data lainnya
        $pelanggan->hasil_kunjungan  = $validated['hasil_kunjungan'] ?? $pelanggan->hasil_kunjungan;
        $pelanggan->telp             = $validated['telp'] ?? $pelanggan->telp;
        $pelanggan->kabel_sl         = $validated['kabel_sl'] ?? $pelanggan->kabel_sl;
        $pelanggan->jenis_sambungan = $validated['jenis_sambungan'] ?? $pelanggan->jenis_sambungan;
        $pelanggan->merk_mcb         = $validated['merk_mcb'] ?? $pelanggan->merk_mcb;
        $pelanggan->ampere_mcb       = $validated['ampere_mcb'] ?? $pelanggan->ampere_mcb;
        $pelanggan->gardu            = $validated['gardu'] ?? $pelanggan->gardu;

        // Info sistem
        $pelanggan->difoto_oleh   = auth()->check() ? auth()->user()->name : 'Guest';
        $pelanggan->tanggal_foto  = now('Asia/Jakarta');
        $pelanggan->verified      = false;

        $pelanggan->save();

        return response()->json(['message' => 'Gambar dan data pelanggan berhasil diperbarui!'], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error("Validasi gagal untuk pelanggan {$id}: " . json_encode($e->errors()));
        return response()->json(['message' => 'Data tidak valid.', 'errors' => $e->errors()], 422);
    } catch (\Throwable $e) {
        Log::error("Gagal update pelanggan {$id}: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
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
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,csv,xls|max:51200' // 50MB max (50*1024 KB)
            ]);

            $file = $request->file('file');

            // Antrikan proses import ke queue
            Excel::queueImport(new PelangganImport, $file);

            return response()->json([
                'message' => 'Data berhasil diimpor!'
            ]);

        } catch (\Exception $e) {
            Log::error('Excel import error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Import failed. ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        if (!$bulan || !$tahun) {
            return redirect()->back()->with('error', 'Bulan dan tahun wajib diisi!');
        }

        return Excel::download(new PelangganExport($bulan, $tahun), "pelanggan_{$bulan}_{$tahun}.xlsx");
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json(['message' => 'Tidak ada ID yang dipilih'], 400);
        }

        try {
            DB::table('pelanggans')->whereIn('id', $ids)->delete();
            return response()->json(['message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus data'], 500);
        }
    }

   public function updateimagecater(Request $request, $id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
        }

        try {
            $validated = $request->validate([
                'gambar_kwh'        => 'nullable|string',
                'kwh_latitude'      => 'nullable|numeric',
                'kwh_longitude'     => 'nullable|numeric',
                'gambar_rumah'      => 'nullable|string',
            ]);

            // Utility closure to process base64 image
            $handleBase64Image = function ($image, $folder, $prefix, $oldPath) {
                if ($image === 'EXISTS_AND_UNCHANGED') {
                    return $oldPath;
                }

                if ($image === 'null') {
                    if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                    return null;
                }

                if ($image) {
                    $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $image);
                    $base64 = str_replace(' ', '+', $base64);

                    $type = 'png';
                    if (preg_match('/^data:image\/(\w+);base64,/', $image, $matches)) {
                        $type = strtolower($matches[1]);
                    }

                    $filename = $prefix . '_' . Str::uuid() . '.' . $type;
                    $path = "pelanggan/{$folder}/{$filename}";

                    Storage::disk('public')->put($path, base64_decode($base64));

                    if ($oldPath && Storage::disk('public')->exists($oldPath) && $oldPath !== $path) {
                        Storage::disk('public')->delete($oldPath);
                    }

                    return $path;
                }

                return $oldPath;
            };

            // Update gambar KWH
            $pelanggan->gambar_kwh = $handleBase64Image(
                $validated['gambar_kwh'] ?? 'EXISTS_AND_UNCHANGED',
                'kwh',
                'kwh',
                $pelanggan->gambar_kwh
            );

            $pelanggan->kwh_latitude = $validated['kwh_latitude'] ?? null;
            $pelanggan->kwh_longitude = $validated['kwh_longitude'] ?? null;

            // Update gambar Rumah
            $pelanggan->gambar_rumah = $handleBase64Image(
                $validated['gambar_rumah'] ?? 'EXISTS_AND_UNCHANGED',
                'rumah',
                'rumah',
                $pelanggan->gambar_rumah
            );

            $pelanggan->difoto_oleh = auth()->check() ? auth()->user()->name : 'Guest';
            $pelanggan->tanggal_foto = now('Asia/Jakarta');
            $pelanggan->verified = false;

            $pelanggan->save();

            return response()->json(['message' => 'Gambar dan data berhasil diperbarui.'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validasi gagal: " . json_encode($e->errors()));
            return response()->json(['message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error("Gagal update pelanggan {$id}: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
            return response()->json(['message' => 'Terjadi kesalahan saat update data.'], 500);
        }
    }

}
