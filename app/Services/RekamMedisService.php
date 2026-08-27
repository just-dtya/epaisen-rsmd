<?php

namespace App\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RekamMedisService
{
    private function getHeaders(): array
    {
        return [
            'clientid' => config('services.erm.client_id', env('ERM_CLIENT_ID')),
            'apitoken' => config('services.erm.api_token', env('ERM_API_TOKEN')),
            'Accept'   => 'application/json',
        ];
    }

    public function getDetailRekamMedisLengkap(string $idPendaftaran, string $idPasien): array
    {
        $baseUrl = rtrim(config('services.erm.base_url', env('ERM_API_BASE_URL')), '/');
        $headers = $this->getHeaders();

        try {
            $responses = Http::pool(fn (Pool $pool) => [
                $pool->as('pemeriksaan')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermpemeriksaan/pendaftaran/{$idPendaftaran}"),
                $pool->as('perawat_pendaftaran')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermperawat/pendaftaran/{$idPendaftaran}"),
                $pool->as('perawat_ttv')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermperawat/ttv/{$idPendaftaran}"),
                $pool->as('refraksi')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermrefraksi/pendaftaran/{$idPendaftaran}"),
                $pool->as('hasil_radiologi')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermhasilradiologi", ['idPendaftaran' => $idPendaftaran]),
                $pool->as('hasil_lab')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/hasillab/pendaftaran/{$idPendaftaran}"),
                $pool->as('penunjang_diagnostik')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermpenunjangdiagnostik/pendaftaran/{$idPendaftaran}"),
                $pool->as('riwayat_penyakit')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermriwayatpenyakit/pasien/{$idPasien}"),
                $pool->as('alergi')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermalergi/pasien/{$idPasien}"),
            ]);

            $result = [];
            foreach ($responses as $key => $response) {
                if ($response instanceof Response && $response->successful()) {
                    // Mencegah error jika response berupa string kosong ""
                    $body = trim($response->body());
                    $jsonContent = !empty($body) ? $response->json() : null;

                    // Normalisasi khusus Radiologi
                    if ($key === 'hasil_radiologi') {
                        $result[$key] = $jsonContent['hasilRadiologi'] ?? $jsonContent ?? [];
                    }
                    // Normalisasi khusus Hasil Lab
                    elseif ($key === 'hasil_lab') {
                        $result[$key] = $jsonContent['hasilLab'] ?? $jsonContent['data'] ?? $jsonContent ?? [];
                    }
                    else {
                        $result[$key] = $jsonContent;
                    }
                } else {
                    $result[$key] = null;
                }
            }

            return $result;

        } catch (Throwable $e) {
            Log::error("Koneksi API ERM Gagal: " . $e->getMessage());
            return [];
        }
    }
}
