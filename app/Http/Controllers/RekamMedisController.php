<?php

namespace App\Http\Controllers;

use App\Services\RekamMedisService;
use App\Services\PenunjangMedisService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RekamMedisController extends Controller
{
    protected RekamMedisService $rekamMedisService;
    protected PenunjangMedisService $penunjangMedisService;

    public function __construct(
        RekamMedisService $rekamMedisService,
        PenunjangMedisService $penunjangMedisService
    ) {
        $this->rekamMedisService = $rekamMedisService;
        $this->penunjangMedisService = $penunjangMedisService;
    }

    /**
     * Helper untuk mendapatkan ID Pasien SIMRS / No RM / No KTP dari user login.
     */
    private function getIdPasien(): ?string
    {
        $user = Auth::user();
        return $user?->id_pasien_simrs ?? $user?->no_rkm_medis ?? $user?->no_ktp;
    }

    /* =========================================================================
     * 1. REKAM MEDIS UTAMA (SOAP / PENDAFTARAN)
     * ========================================================================= */

    public function show(string $id_pendaftaran): Response
    {
        $idPasien = $this->getIdPasien();
        $rawErm = $this->rekamMedisService->getDetailRekamMedisLengkap($id_pendaftaran, $idPasien);
        $rawErm = is_array($rawErm) ? $rawErm : [];

        $formattedErm = [
            'soap_dokter'         => $rawErm['pemeriksaan'] ?? null,
            'perawat_pemeriksaan' => $rawErm['perawat_pendaftaran'] ?? null,
            'perawat_ttv'         => $rawErm['perawat_ttv'] ?? null,
            'refraksi'            => $rawErm['refraksi']['refraksi'] ?? $rawErm['refraksi'] ?? null,
            'penunjang'           => $rawErm['penunjang_diagnostik']['penunjangDiagnostik'] ?? $rawErm['penunjang_diagnostik'] ?? null,
            'riwayat_penyakit'    => $rawErm['riwayat_penyakit']['ermRiwayatPenyakit'] ?? $rawErm['riwayat_penyakit'] ?? [],
            'alergi'              => $rawErm['alergi']['ermAlergi'] ?? $rawErm['alergi'] ?? [],
        ];

        return Inertia::render('RekamMedisView', [
            'id_pendaftaran' => $id_pendaftaran,
            'rekam_medis'    => $formattedErm,
            'error'          => empty(array_filter($formattedErm)) ? 'Gagal mengambil data rekam medis dari server ERM.' : null,
        ]);
    }

    /* =========================================================================
     * 2. MENU LABORATORIUM (LIST & DETAIL)
     * ========================================================================= */

    public function indexLab(): Response
    {
        $idPasien = $this->getIdPasien();
        $listPendaftaran = [];

        if ($idPasien) {
            try {
                // Mengambil riwayat pendaftaran dari database SIMRS lokal
                $listPendaftaran = DB::connection('simrs')
                    ->table('dt_pendaftaran as p')
                    ->leftJoin('dt_jadwal_praktik_dokter as jpd', 'p.id_jadwal_praktik_dokter', '=', 'jpd.id_jadwal_praktik_dokter')
                    ->leftJoin('dt_dokter as d', 'jpd.id_dokter', '=', 'd.id_dokter')
                    ->leftJoin('dt_ruangan as r', 'jpd.id_ruangan', '=', 'r.id_ruangan')
                    ->leftJoin('dt_poli as poli', 'r.id_poli', '=', 'poli.id_poli')
                    ->where('p.id_pasien', $idPasien)
                    ->where('p.sts_batal', 0)
                    ->orderBy('p.tgl_periksa', 'desc')
                    ->select([
                        'p.id_pendaftaran',
                        'p.tgl_periksa',
                        'd.nama_dokter',
                        'poli.deskripsi_poli',
                    ])
                    ->get();
            } catch (\Throwable $e) {
                $listPendaftaran = [];
            }
        }

        $listRiwayatLab = [];

        // Looping pendaftaran untuk menyaring kunjungan yang memiliki hasil lab
        foreach ($listPendaftaran as $pendaftaran) {
            $idPendaftaran = $pendaftaran->id_pendaftaran;

            if ($idPendaftaran) {
                $hasilLab = $this->penunjangMedisService->getHasilLabByPendaftaran($idPendaftaran);

                if (!empty($hasilLab)) {
                    $listRiwayatLab[] = [
                        'id_pendaftaran'  => $idPendaftaran,
                        'tgl_pemeriksaan' => date('d M Y', strtotime($pendaftaran->tgl_periksa)),
                        'nama_poli'       => $pendaftaran->deskripsi_poli ?: 'Poliklinik',
                        'dokter'          => $pendaftaran->nama_dokter ?: 'Dokter Pemeriksa',
                    ];
                }
            }
        }

        return Inertia::render('Laboratorium/Index', [
            'listRiwayat' => $listRiwayatLab,
        ]);
    }

    public function showLab(string $id_pendaftaran): Response
    {
        $hasilLab = $this->penunjangMedisService->getHasilLabByPendaftaran($id_pendaftaran);

        return Inertia::render('Laboratorium/Show', [
            'id_pendaftaran' => $id_pendaftaran,
            'hasilLab'       => is_array($hasilLab) ? $hasilLab : [],
            'error'          => empty($hasilLab) ? 'Data laboratorium tidak ditemukan untuk pendaftaran ini.' : null,
        ]);
    }

    /* =========================================================================
     * 3. MENU RADIOLOGI (LIST & DETAIL)
     * ========================================================================= */

    public function indexRadiologi(): Response
    {
        $idPasien = $this->getIdPasien();
        $listPendaftaran = [];

        if ($idPasien) {
            try {
                // Mengambil riwayat pendaftaran dari database SIMRS lokal
                $listPendaftaran = DB::connection('simrs')
                    ->table('dt_pendaftaran as p')
                    ->leftJoin('dt_jadwal_praktik_dokter as jpd', 'p.id_jadwal_praktik_dokter', '=', 'jpd.id_jadwal_praktik_dokter')
                    ->leftJoin('dt_dokter as d', 'jpd.id_dokter', '=', 'd.id_dokter')
                    ->leftJoin('dt_ruangan as r', 'jpd.id_ruangan', '=', 'r.id_ruangan')
                    ->leftJoin('dt_poli as poli', 'r.id_poli', '=', 'poli.id_poli')
                    ->where('p.id_pasien', $idPasien)
                    ->where('p.sts_batal', 0)
                    ->orderBy('p.tgl_periksa', 'desc')
                    ->select([
                        'p.id_pendaftaran',
                        'p.tgl_periksa',
                        'd.nama_dokter',
                        'poli.deskripsi_poli',
                    ])
                    ->get();
            } catch (\Throwable $e) {
                $listPendaftaran = [];
            }
        }

        $listRiwayatRadiologi = [];

        // Looping pendaftaran untuk menyaring kunjungan yang memiliki hasil radiologi
        foreach ($listPendaftaran as $pendaftaran) {
            $idPendaftaran = $pendaftaran->id_pendaftaran;

            if ($idPendaftaran) {
                $hasilRadiologi = $this->penunjangMedisService->getHasilRadiologiByPendaftaran($idPendaftaran);

                if (!empty($hasilRadiologi)) {
                    $listRiwayatRadiologi[] = [
                        'id_pendaftaran'  => $idPendaftaran,
                        'tgl_pemeriksaan' => date('d M Y', strtotime($pendaftaran->tgl_periksa)),
                        'nama_poli'       => $pendaftaran->deskripsi_poli ?: 'Poliklinik',
                        'dokter'          => $pendaftaran->nama_dokter ?: 'Dokter Pemeriksa',
                    ];
                }
            }
        }

        return Inertia::render('Radiologi/Index', [
            'listRiwayat' => $listRiwayatRadiologi,
        ]);
    }

    public function showRadiologi(string $id_pendaftaran): Response
    {
        $hasilRadiologi = $this->penunjangMedisService->getHasilRadiologiByPendaftaran($id_pendaftaran);

        return Inertia::render('Radiologi/Show', [
            'id_pendaftaran' => $id_pendaftaran,
            'hasilRadiologi' => is_array($hasilRadiologi) ? $hasilRadiologi : [],
            'error'          => empty($hasilRadiologi) ? 'Data pemeriksaan radiologi tidak ditemukan untuk pendaftaran ini.' : null,
        ]);
    }
}
