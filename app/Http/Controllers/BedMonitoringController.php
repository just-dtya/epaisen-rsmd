<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class BedMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $apiUrl = 'https://web.rsmdsr.id/api/bed-status';

        // Cache 2 menit agar live data tetap up to date tanpa membebani server
        $bedData = Cache::remember('bed_monitoring_data', 120, function () use ($apiUrl) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $result = json_decode($response, true);
                if (isset($result['status']) && $result['status'] && isset($result['data'])) {
                    return $result['data'];
                }
            }

            return [];
        });

        // Hitung total keseluruhan bed ranap
        $totalSemua = collect($bedData)->sum('total');
        $totalKosong = collect($bedData)->sum('jumlah_kosong');
        $totalTerisi = collect($bedData)->sum('jumlah_terisi');

        return Inertia::render('BedMonitoringView', [
            'beds'        => $bedData,
            'summary'     => [
                'total'  => $totalSemua,
                'kosong' => $totalKosong,
                'terisi' => $totalTerisi,
            ],
            'lastUpdated' => date('d M Y, H:i') . ' WIB',
        ]);
    }
}
