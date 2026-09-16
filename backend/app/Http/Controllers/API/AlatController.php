<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alat\StoreAlatRequest;
use App\Http\Requests\Alat\UpdateAlatRequest;
use App\Http\Resources\AlatResource;
use App\Models\Alat;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : JsonResponse
    {
        //tetap menggunakan eager loading untuk mencegah h+1 query 
        $alat = Alat::with('kategori')->latest()->get();
        return response()->json([
            'message' => 'Daftar alat berhasil diambil.',
            'data' => AlatResource::collection($alat)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlatRequest $request) : JsonResponse
    {
        $data = $request->validated();
        $alat = DB::transaction(function () use ($request, $data) {
            if ($request->hasFile('gambar')) {
                $data['gambar'] = $request->file('gambar')->store('alat', 'public');
            }
            return Alat::create($data);
        });
        return response()->json([
            'message' => 'Alat berhasil ditambahkan.',
            'data' => new AlatResource($alat->load('kategori'))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Alat $alat) : JsonResponse
    {
        //mebggunakan route model binding ($alat) dikombinasikan dengan load()
        return response()->json([
            'data' => new AlatResource($alat->load('kategori'))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlatRequest $request, Alat $alat) : JsonResponse
    {
        $data = $request->validated();
        $oldGambar = $alat->gambar;//simpan path gambar lama lebih dahulu

        DB::transaction(function () use ($request, &$data, $alat, $oldGambar) {
            if ($request->hasFile('gambar')) {
                $data['gambar'] = $request->file('gambar')->store('alat', 'public');
                //hapus gambar lama dari server hanya setelah gambar baru sukses masuk ke database
                if ($oldGambar) {
                    Storage::disk('public')->delete($oldGambar);
                }
            }
            $alat->update($data);
        });
        return response()->json([
            'message' => 'Alat berhasil diperbarui.',
            'data' => new AlatResource($alat->load('kategori'))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alat $alat) : JsonResponse
    {
        DB::transaction(function () use ($alat) {
            if ($alat->gambar) {
                Storage::disk('public')->delete($alat->gambar);
            }
            $alat->delete();
        });
        return response()->json([
            'message' => 'Alat berhasil dihapus.'
        ]);
    }

    public function katalog() : JsonResponse
    {
        $alat = Alat::with('kategori')->tersedia()->latest()->get();
        return response()->json([
            'message' => 'Katalog alat tersedia.',
            'data' => AlatResource::collection($alat)
        ]);
    }
}
