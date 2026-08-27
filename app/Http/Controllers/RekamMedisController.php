<?php

namespace App\Http\Controllers;

use App\Services\RekamMedisService;
use Illuminate\Http\Request;
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
        $dataRekamMedis = $this->rekamMedisService->getRekamMedisByIdPendaftaran($id_pendaftaran);

        return Inertia::render('RekamMedisView', [
            'id_pendaftaran' => $id_pendaftaran,
            'rekam_medis'    => $dataRekamMedis,
            'error'          => $dataRekamMedis === null ? 'Gagal mengambil data rekam medis dari server ERM.' : null,
        ]);
    }
}
