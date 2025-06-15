<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ControlController extends Controller
{
    public function reset()
    {
        // Kosongkan cache atau session atau logika reset lainnya
        Cache::forget('buzzer');
        Cache::forget('led');

        return redirect()->back()->with('status', 'Sistem berhasil direset.');
    }

    public function toggleBuzzer(Request $request)
    {
        $state = $request->input('state'); // on / off
        Cache::put('buzzer', $state);
        return response()->json(['status' => 'ok', 'buzzer' => $state]);
    }

    public function toggleLed(Request $request)
    {
        $state = $request->input('state'); // on / off
        Cache::put('led', $state);
        return response()->json(['status' => 'ok', 'led' => $state]);
    }
}
