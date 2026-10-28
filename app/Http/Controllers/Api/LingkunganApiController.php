<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PemantauanLingkungan;
use Carbon\Carbon;

class LingkunganApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = PemantauanLingkungan::with(['departemen', 'unitKerja']);

            // Filter Tanggal
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal_pemantauan', [$request->start_date, $request->end_date]);
            }

            if ($request->has('location') && $request->location != 'Semua') {
                $query->where('area', $request->location);
            }

            if ($request->has('department') && $request->department != 'Semua') {
                $query->whereHas('departemen', function ($q) use ($request) {
                    $q->where('nama_departemen', $request->department);
                });
            }

            if ($request->has('unit_kerja') && $request->unit_kerja != 'Semua') {
                $query->whereHas('unitKerja', function ($q) use ($request) {
                    $q->where('nama_unit_kerja', $request->unit_kerja);
                });
            }

            // Gunakan pagination (Default 15 data per halaman)
            $perPage = $request->input('per_page', 15);
            $data = $query->orderBy('tanggal_pemantauan', 'desc')->paginate($perPage);

            $transformedData = $data ->getCollection()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'location' => $item->area,
                    'sub_area' => $item->lokasi,
                    'department' => $item->departemen->nama_departemen ?? 'N/A',
                    'unit_kerja' => $item->unitKerja->nama_unit_kerja ?? 'N/A',
                    'tanggal' => Carbon::parse($item->tanggal_pemantauan)->format('d M Y'),
                    'kesimpulan' => $item->kesimpulan ?? 'N/A', // REVISI: Ambil dari kolom database
                    
                    'cahaya_lux' => $item->data_pemantauan['cahaya'] ?? 0,
                    'bising_db' => $item->data_pemantauan['bising'] ?? 0,
                    'debu_mg_nm3' => $item->data_pemantauan['debu'] ?? 0,
                    
                    // REVISI: Samakan key JSON dengan model di Flutter
                    'suhu_basah' => $item->data_pemantauan['suhu_basah'] ?? 'N/A',
                    'suhu_kering' => $item->data_pemantauan['suhu_kering'] ?? 'N/A',
                    'suhu_radiasi' => $item->data_pemantauan['suhu_radiasi'] ?? 'N/A',
                    'suhu_indoor' => $item->data_pemantauan['isbb_indoor'] ?? 'N/A',
                    'suhu_outdoor' => $item->data_pemantauan['isbb_outdoor'] ?? 'N/A',
                    'rh' => $item->data_pemantauan['rh'] ?? 'N/A',
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedData,
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'total' => $data->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getFilters()
    {
        try {
            // Ambil Area unik
            $areas = PemantauanLingkungan::distinct()->pluck('area')->toArray();
            array_unshift($areas, 'Semua');

            // Ambil Departemen
            $departments = \App\Models\Departemen::pluck('nama_departemen')->toArray();
            array_unshift($departments, 'Semua');

            // Ambil Unit Kerja
            $units = \App\Models\UnitKerja::pluck('nama_unit_kerja')->toArray();
            array_unshift($units, 'Semua');

            return response()->json([
                'status' => 'success',
                'areas' => $areas,
                'departments' => $departments,
                'units' => $units
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}