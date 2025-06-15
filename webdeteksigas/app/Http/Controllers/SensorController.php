<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Models\Threshold;

class SensorController extends Controller
{
    // Halaman dashboard monitoring (web)
    public function index()
    {
        $latest = SensorData::latest()->first();
        $history = SensorData::orderBy('created_at', 'desc')->limit(10)->get()->reverse();
        $threshold = Threshold::first()?->value ?? 300;

        return view('monitoring.dashboard', compact('latest', 'history', 'threshold'));
    }

    // Simpan data sensor (POST /api/sensors)
    public function store(Request $request)
    {
        $data = $request->validate([
            'gas_level' => 'required|numeric',
            'fire_level' => 'required|boolean',
        ]);

        $sensorData = SensorData::create($data);
        return response()->json(['message' => 'Data sensor disimpan', 'data' => $sensorData], 201);
    }

    // Ambil data sensor terbaru (GET /api/sensors/latest)
    public function latest()
    {
        $latest = SensorData::latest()->first();
        return response()->json($latest);
    }

    // Ambil data sensor historis (GET /api/sensors/history)
    public function history()
    {
        $history = SensorData::orderBy('created_at', 'desc')->limit(100)->get();
        return response()->json($history);
    }
}
