<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class TarifPelayananController extends Controller
{
    public function index(Request $request)
    {
        $apiUrl = 'https://daftaronline.rsmdsrjatengprov.id/api/tarif_pelayanan/get_view_tabel?ftarif=';

        $dataTarif = Cache::remember('tarif_rsmd_data', 1800, function () use ($apiUrl) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $result = json_decode($response, true);
                if (isset($result['Status']) && $result['Status'] === '000' && isset($result['Data']['tarif'])) {
                    return $result['Data']['tarif'];
                }
            }

            return [];
        });

        return Inertia::render('TarifPelayananView', [
            'tarif' => $dataTarif,
        ]);
    }
}
