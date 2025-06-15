<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DeviceControlController extends Controller
{
    /**
     * Mengontrol perangkat (LED, buzzer, kipas) berdasarkan permintaan dari frontend.
     */
    public function control(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'device' => 'required|string|in:led,buzzer,fan',
            'state' => 'required|boolean', // true = ON, false = OFF
        ]);

        $device = $validated['device'];
        $state = $validated['state'];

        // Ambil IP board dari .env
        $espIp = env('ESP_IP', '192.168.1.50');
        $url = "http://{$espIp}/control";

        try {
            // Kirim HTTP request ke board (ESP8266/ESP32)
            $response = Http::timeout(5)->get($url, [
                'device' => $device,
                'state' => $state ? 'on' : 'off',
            ]);

            // Log untuk debugging
            Log::info('Mengirim perintah ke ESP', [
                'url' => $url,
                'params' => [
                    'device' => $device,
                    'state' => $state ? 'on' : 'off',
                ],
                'response_status' => $response->status(),
                'response_body' => $response->body(),
            ]);

            if ($response->successful()) {
                return response()->json([
                    'message' => "Perangkat '$device' berhasil diatur ke " . ($state ? 'ON' : 'OFF'),
                    'response' => $response->body()
                ], 200);
            } else {
                return response()->json([
                    'message' => "Gagal menghubungi perangkat.",
                    'error' => $response->body()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengontrol perangkat', [
                'error' => $e->getMessage(),
                'url' => $url,
            ]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengontrol perangkat.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
