<?php

namespace App\Http\Controllers;

use App\Services\RekamMedisService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RekamMedisController extends Controller
{
    protected $rekamMedisService;

    public function __construct(RekamMedisService $rekamMedisService)
    {
        $this->rekamMedisService = $rekamMedisService;
    }

    public function show($id_pendaftaran)
    {
        $user = Auth::user();
        $idPasien = $user->id_pasien_simrs ?? $user->no_ktp;

        $rawErm = $this->rekamMedisService->getDetailRekamMedisLengkap($id_pendaftaran, $idPasien);

        $formattedErm = [
            'soap_dokter' => $rawErm['pemeriksaan'] ?? null,
            'perawat_pemeriksaan' => $rawErm['perawat_pendaftaran'] ?? null,
            'perawat_ttv' => $rawErm['perawat_ttv'] ?? null,
            'refraksi' => $rawErm['refraksi']['refraksi'] ?? $rawErm['refraksi'] ?? null,

            // Ubah nama key menjadi camelCase agar sesuai dengan Prop Vue
            'hasilRadiologi' => is_array($rawErm['hasil_radiologi']) ? $rawErm['hasil_radiologi'] : [],
            'hasilLab' => is_array($rawErm['hasil_lab']) ? $rawErm['hasil_lab'] : [],

            'penunjang' => $rawErm['penunjang_diagnostik']['penunjangDiagnostik'] ?? $rawErm['penunjang_diagnostik'] ?? null,
            'riwayat_penyakit' => $rawErm['riwayat_penyakit']['ermRiwayatPenyakit'] ?? $rawErm['riwayat_penyakit'] ?? [],
            'alergi' => $rawErm['alergi']['ermAlergi'] ?? $rawErm['alergi'] ?? [],
        ];


        return Inertia::render('RekamMedisView', [
            'id_pendaftaran' => $id_pendaftaran,
            'rekam_medis' => $formattedErm,
            'error' => empty(array_filter($formattedErm)) ? 'Gagal mengambil data rekam medis dari server ERM.' : null,
        ]);
    }
}
