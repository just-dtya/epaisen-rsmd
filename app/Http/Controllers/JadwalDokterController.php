<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class JadwalDokterController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', date('Y-m-d'));

        // Simpan cache selama 10 menit berdasarkan tanggal
        $dataJadwal = Cache::remember("jadwal_dokter_{$tanggal}", 600, function () use ($tanggal) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(5) // Kurangi timeout dari 10s ke 5s agar worker tidak tertahan lama jika API down
                    ->get('https://dashboard.rsmdsr.id/api/jadwal-dokter', [
                        'tanggal' => $tanggal,
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Throwable $e) {
                // Log error jika diperlukan: \Log::error($e->getMessage());
            }

            return [];
        });

        return Inertia::render('JadwalDokterView', [
            'jadwal' => $dataJadwal,
            'selectedDate' => $tanggal,
        ]);
    }
}
