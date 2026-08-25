<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            // Shared Auth Data (Gunakan closure agar dievaluasi saat session ready)
            'auth' => [
                'user' => fn () => $request->user(),
                'patient' => function () use ($request) {
                    $user = $request->user();
                    if (! $user) {
                        return null;
                    }

                    return [
                        'id' => $user->id,
                        'id_pasien_simrs' => $user->id_pasien_simrs ?? null,
                        'no_rkm_medis' => $user->no_rkm_medis ?? $user->no_rm ?? null,
                        'nama' => $user->name ?? $user->nama_pasien ?? 'Pasien RSMD',
                        'no_ktp' => $user->no_ktp ?? $user->nik ?? null,
                        'jk' => $user->jk ?? 'L',
                        'tmp_lahir' => $user->tmp_lahir ?? '-',
                        'tgl_lahir' => $user->tgl_lahir ? date('d-m-Y', strtotime($user->tgl_lahir)) : null,
                        'nm_ibu' => $user->nm_ibu ?? '-',
                        'no_tlp' => $user->no_tlp ?? '-',
                        'alamat' => $user->alamat ?? '-',
                    ];
                },
            ],

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
        ],
        ];
    }
}
