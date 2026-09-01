<?php

namespace App\Services;

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

    private function getBaseUrl(): string
    {
        return rtrim(config('services.erm.base_url', env('ERM_API_BASE_URL')), '/');
    }

    /**
     * Mengambil Detail Rekam Medis Lengkap (SOAP, Perawat, TTV, Refraksi, Riwayat Penyakit, Alergi)
     */
    public function getDetailRekamMedisLengkap(string $idPendaftaran, string $idPasien): array
    {
        $baseUrl = $this->getBaseUrl();
        $headers = $this->getHeaders();

        try {
            $responses = Http::pool(fn ($pool) => [
                $pool->as('pemeriksaan')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermpemeriksaan/pendaftaran/{$idPendaftaran}"),
                $pool->as('perawat_pendaftaran')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermperawat/pendaftaran/{$idPendaftaran}"),
                $pool->as('perawat_ttv')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermperawat/ttv/{$idPendaftaran}"),
                $pool->as('refraksi')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermrefraksi/pendaftaran/{$idPendaftaran}"),
                $pool->as('penunjang_diagnostik')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermpenunjangdiagnostik/pendaftaran/{$idPendaftaran}"),
                $pool->as('riwayat_penyakit')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermriwayatpenyakit/pasien/{$idPasien}"),
                $pool->as('alergi')->timeout(10)->withHeaders($headers)->get("{$baseUrl}/ermalergi/pasien/{$idPasien}"),
            ]);

            $result = [];
            foreach ($responses as $key => $response) {
                if ($response instanceof Response && $response->successful()) {
                    $body = trim($response->body());
                    $result[$key] = !empty($body) ? $response->json() : null;
                } else {
                    $result[$key] = null;
                }
            }

            return $result;
        } catch (Throwable $e) {
            Log::error('Koneksi API ERM Gagal: ' . $e->getMessage());
            return [];
        }
    }

    public function getRiwayatPendaftaranPasien(string $idPasien): array
    {
        try {
            $baseUrl = $this->getBaseUrl();
            $headers = $this->getHeaders();

            // Sesuaikan endpoint ini dengan rute riwayat kunjungan di API ERM Anda
            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->get("{$baseUrl}/pendaftaran/pasien/{$idPasien}"); // Ganti path jika endpoint aslinya berbeda

            if ($response->successful()) {
                $data = $response->json();
                return is_array($data) ? ($data['listPendaftaran'] ?? $data['data'] ?? $data) : [];
            }
            return [];
        } catch (Throwable $e) {
            Log::error("Gagal mengambil riwayat pendaftaran pasien: " . $e->getMessage());
            return [];
        }
    }
}
