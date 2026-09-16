<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use APP\Http\Resources\PeminjamanResource;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : JsonResponse
    {
        // 1. validasi input parameter
        $validator = Validator::make($request->all(), [
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'status' =>['nullable', 'string', 'in:diajukan,dipinjam,dikembalikan,telat'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Parameter filter tidak valid.',
                'errors' => $validator->errors()
            ], 422);
            // 2 eager loading untuk mencegah masalah n+! query
            $query = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian.petugas']);
            //filter rentang tanggal pinjam
            $query->when($request->filled('start_date') && $request->filled('end_date'), function ($q) use ($request) {
                $q->whereBetween('tgl_pinjam', [$request->start_date, $request->end_date]);
            //filter status peminjaman
            $query->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            });
            // pagination
            $perPage = $request->input('per_page', 15);
            $laporan = $query->latest()->paginate($perPage);
            //api resource khusus untuk instance paginate
            return PeminjamanResource::collection($laporan)->additional([
                'message' => 'Laporan peminjaman berhasil ditarik.'
            ])
            ->response();
            })
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
