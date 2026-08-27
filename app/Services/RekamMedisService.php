<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RekamMedisService
{
    public function getRekamMedisByIdPendaftaran(string $idPendaftaran): ?array
    {
        $baseUrl  = config('services.erm.base_url', env('ERM_API_BASE_URL'));
        $clientId = config('services.erm.client_id', env('ERM_CLIENT_ID'));
        $apiToken = config('services.erm.api_token', env('ERM_API_TOKEN'));

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'clientid' => $clientId,
                    'apitoken' => $apiToken,
                    'Accept'   => 'application/json',
                ])
                ->get("{$baseUrl}/ermpemeriksaan/pendaftaran/{$idPendaftaran}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("ERM API Error [{$response->status()}]: " . $response->body());
        } catch (\Throwable $e) {
            Log::error("Koneksi API ERM Gagal: " . $e->getMessage());
        }

        return null;
    }
}
