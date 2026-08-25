<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HistoryKunjunganController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $idPasien = $user->id_pasien_simrs;

        $riwayat = [];

        if ($idPasien) {
            try {
                $riwayat = DB::connection('simrs')->table('dt_pendaftaran as p')
                    // 1. Join Jenis Layanan
                    ->leftJoin('dt_jenis_layanan as jl', 'p.id_jenis_layanan', '=', 'jl.id_jenis_layanan')
                    // 2. Join Penjamin (ds_penjamin)
                    ->leftJoin('ds_penjamin as pj', 'p.id_penjamin', '=', 'pj.id_penjamin')
                    // 3. Join Jadwal Praktik Dokter
                    ->leftJoin('dt_jadwal_praktik_dokter as jpd', 'p.id_jadwal_praktik_dokter', '=', 'jpd.id_jadwal_praktik_dokter')
                    // 4. Join Dokter
                    ->leftJoin('dt_dokter as d', 'jpd.id_dokter', '=', 'd.id_dokter')
                    // 5. Join Hari (ds_hari)
                    ->leftJoin('ds_hari as h', 'jpd.id_hari', '=', 'h.id_hari')
                    // 6. Join Ruangan (dt_ruangan)
                    ->leftJoin('dt_ruangan as r', 'jpd.id_ruangan', '=', 'r.id_ruangan')
                    // 7. Join Poli (dt_poli)
                    ->leftJoin('dt_poli as poli', 'r.id_poli', '=', 'poli.id_poli')
                    // 8. Join Instalasi
                    ->leftJoin('instalasi as inst', 'poli.id_instalasi', '=', 'inst.id_instalasi')
                    ->where('p.id_pasien', $idPasien)
                    ->orderBy('p.tgl_periksa', 'desc')
                    ->orderBy('p.created_at', 'desc')
                    ->select([
                        'p.id_pendaftaran',
                        'p.tgl_periksa',
                        'p.no_antrian',
                        'p.no_rujukan',
                        'p.estimasi_kehadiran',
                        'p.sts_presensi',
                        'p.presensi_at',
                        'p.sts_periksa',
                        'p.periksa_at',
                        'p.created_at',
                        // Master Joins
                        'jl.nama_jenis_layanan',
                        'pj.nama_penjamin',
                        'd.nama_dokter',
                        'h.nama_hari',
                        'jpd.jam_mulai_praktik',
                        'jpd.jam_selesai_praktik',
                        'r.deskripsi_ruangan',
                        'poli.deskripsi_poli',
                        'inst.nama_instalasi',
                    ])
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id_pendaftaran'      => $item->id_pendaftaran,
                            'tgl_periksa_raw'     => $item->tgl_periksa,
                            'tgl_periksa'         => date('d M Y', strtotime($item->tgl_periksa)),
                            'nama_hari'           => $item->nama_hari ?: null,
                            'no_antrian'          => $item->no_antrian ?: '-',
                            'nama_jenis_layanan'  => $item->nama_jenis_layanan ?: 'Rawat Jalan',
                            'nama_penjamin'       => $item->nama_penjamin ?: 'Umum',
                            'nama_dokter'         => $item->nama_dokter ?: 'Dokter Poliklinik',
                            'jam_praktik'         => ($item->jam_mulai_praktik && $item->jam_selesai_praktik)
                                ? substr($item->jam_mulai_praktik, 0, 5) . ' - ' . substr($item->jam_selesai_praktik, 0, 5) . ' WIB'
                                : '-',
                            'deskripsi_ruangan'   => $item->deskripsi_ruangan ?: '-',
                            'deskripsi_poli'      => $item->deskripsi_poli ?: 'Poliklinik',
                            'nama_instalasi'      => $item->nama_instalasi ?: 'Instalasi Rawat Jalan',
                            'no_rujukan'          => $item->no_rujukan ?: '-',
                            'estimasi_kehadiran'  => $item->estimasi_kehadiran ? substr($item->estimasi_kehadiran, 0, 5) : '-',
                            'sts_presensi'        => (int) $item->sts_presensi,
                            'presensi_at'         => $item->presensi_at ? date('H:i', strtotime($item->presensi_at)) : null,
                            'sts_periksa'         => (int) $item->sts_periksa,
                            'periksa_at'          => $item->periksa_at ? date('H:i', strtotime($item->periksa_at)) : null,
                        ];
                    });
            } catch (\Throwable $e) {
                $riwayat = [];
            }
        }

        return Inertia::render('HistoryKunjunganView', [
            'riwayat' => $riwayat,
        ]);
    }
}
