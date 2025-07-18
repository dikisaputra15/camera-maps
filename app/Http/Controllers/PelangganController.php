<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

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
        $id_pel = $request->id_pel;
        $no_meter = $request->no_meter;

        $data = DB::table('pelanggans')
            ->where('id_pel', $id_pel)
            ->where('no_meter', $no_meter)
            ->where(function ($query) {
                $query->whereNull('gambar_kwh')
                    ->orWhere('gambar_kwh', '')
                    ->orWhereNull('gambar_rumah')
                    ->orWhere('gambar_rumah', '');
            })
            ->get();

        if ($data->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'html' => view('pelanggan.result-pelanggan', compact('data'))->render()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Semua gambar lengkap, tidak ada data kosong.'
        ]);
    }

     public function formupload($id)
    {
        $pelanggan = Pelanggan::find($id);

        return view('pelanggan.formupload', compact('pelanggan'));
    }
}
