<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PenunjangMedisService
{
    private function getHeaders(): array
    {
        return [
            'clientid' => config('services.erm.client_id', env('ERM_CLIENT_ID')),
            'apitoken' => config('services.erm.api_token', env('ERM_API_TOKEN')),
            'Accept'   => 'application/json',
        ];
    }

    private function getBaseUrl(): string
    {
        return rtrim(config('services.erm.base_url', env('ERM_API_BASE_URL')), '/');
    }

    /* =========================================================================
     * LABORATORIUM (Berbasis Pendaftaran / Kunjungan)
     * ========================================================================= */

    /**
     * Ambil detail hasil Lab berdasarkan ID Pendaftaran kunjungan tertentu.
     */
    public function getHasilLabByPendaftaran(string $idPendaftaran): array
    {
        try {
            $baseUrl = $this->getBaseUrl();
            $response = Http::timeout(10)
                ->withHeaders($this->getHeaders())
                ->get("{$baseUrl}/hasillab/pendaftaran/{$idPendaftaran}");

            if ($response->successful()) {
                $jsonContent = $response->json();

                if (is_array($jsonContent)) {
                    // Mengambil item pemeriksaan lab sesuai struktur JSON dari API server
                    return $jsonContent['itemsPemeriksaanBerkasPasienRujukanDalamPenunjang']
                        ?? $jsonContent['hasilLab']
                        ?? $jsonContent['data']
                        ?? [];
                }
            }
            return [];
        } catch (Throwable $e) {
            Log::error("Gagal mengambil detail Lab (Pendaftaran: {$idPendaftaran}): " . $e->getMessage());
            return [];
        }
    }

    /* =========================================================================
     * RADIOLOGI (Berbasis Pendaftaran / Kunjungan)
     * ========================================================================= */

    /**
     * Ambil detail hasil Radiologi berdasarkan ID Pendaftaran kunjungan tertentu.
     */
    public function getHasilRadiologiByPendaftaran(string $idPendaftaran): array
    {
        try {
            $baseUrl = $this->getBaseUrl();
            $response = Http::timeout(10)
                ->withHeaders($this->getHeaders())
                ->get("{$baseUrl}/ermhasilradiologi", ['idPendaftaran' => $idPendaftaran]);

            if ($response->successful()) {
                $jsonContent = $response->json();

                if (is_array($jsonContent)) {
                    return $jsonContent['hasilRadiologi']
                        ?? $jsonContent['data']
                        ?? $jsonContent
                        ?? [];
                }
            }
            return [];
        } catch (Throwable $e) {
            Log::error("Gagal mengambil detail Radiologi (Pendaftaran: {$idPendaftaran}): " . $e->getMessage());
            return [];
        }
    }
}
