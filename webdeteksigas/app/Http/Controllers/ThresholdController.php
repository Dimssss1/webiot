<?php

// app/Http/Controllers/ThresholdController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Threshold;

class ThresholdController extends Controller
{
    public function get()
    {
        $threshold = Threshold::latest()->first();
        return response()->json($threshold);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'gas_threshold' => 'required|numeric',
            'fire_threshold' => 'required|boolean',
        ]);

        // update threshold terakhir
        $threshold = Threshold::latest()->first();
        if (!$threshold) {
            $threshold = Threshold::create($data);
        } else {
            $threshold->update($data);
        }

        return response()->json(['message' => 'Threshold diperbarui', 'data' => $threshold]);
    }
}
