<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PatientAuthController extends Controller
{
    /**
     * Halaman Login Pasien
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return Inertia::render('LoginPage');
    }

    /**
     * Halaman Registrasi Pasien Baru
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return Inertia::render('RegisterPage');
    }

    /**
     * Proses Verifikasi & Login Pasien dari SIMRS (bkim_dbmain)
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'no_identitas' => 'required|string',
            'tgl_lahir'    => 'required|date',
        ], [
            'no_identitas.required' => 'Nomor Rekam Medis atau NIK wajib diisi.',
            'tgl_lahir.required'    => 'Tanggal lahir wajib dipilih.',
        ]);

        $rawIdentitas = trim($validated['no_identitas']);
        $tglLahir     = date('Y-m-d', strtotime($validated['tgl_lahir']));

        // Normalisasi format 6 digit No. RM (contoh: 4662 -> 004662)
        $unpaddedRm = ltrim($rawIdentitas, '0');
        $paddedRm   = is_numeric($rawIdentitas) ? str_pad($unpaddedRm, 6, '0', STR_PAD_LEFT) : $rawIdentitas;

        try {
            // 1. Cek di Database Lokal terlebih dahulu
            $localUser = User::where(function ($q) use ($rawIdentitas, $paddedRm, $unpaddedRm) {
                $q->whereIn('no_rkm_medis', [$rawIdentitas, $paddedRm, $unpaddedRm])
                  ->orWhere('no_ktp', $rawIdentitas);
            })->whereDate('tgl_lahir', $tglLahir)->first();

            // 2. Jika belum ada atau perlu disinkronkan, tarik dari Database SIMRS (bkim_dbmain.dt_pasien)
            if (!$localUser || empty($localUser->id_pasien_simrs)) {
                $simrsPatient = DB::connection('simrs')->table('dt_pasien')
                    ->where(function ($q) use ($rawIdentitas, $paddedRm, $unpaddedRm) {
                        $q->whereIn('no_rm_pasien', [$rawIdentitas, $paddedRm, $unpaddedRm])
                          ->orWhere('nik_pasien', $rawIdentitas);
                    })
                    ->whereDate('lahir_tgl_pasien', $tglLahir)
                    ->first();

                if (!$simrsPatient && !$localUser) {
                    return back()->withErrors([
                        'login_error' => "Data pasien [{$rawIdentitas}] dengan tanggal lahir [{$tglLahir}] tidak ditemukan di SIMRS.",
                    ]);
                }

                if ($simrsPatient) {
                    // Format alamat lengkap
                    $alamatLengkap = collect([
                        $simrsPatient->alamat_jln_pasien ?? null,
                        ($simrsPatient->alamat_rt_pasien || $simrsPatient->alamat_rw_pasien)
                            ? 'RT ' . ($simrsPatient->alamat_rt_pasien ?? '-') . ' / RW ' . ($simrsPatient->alamat_rw_pasien ?? '-')
                            : null,
                        $simrsPatient->alamat_kel_pasien ? 'Kel. ' . $simrsPatient->alamat_kel_pasien : null,
                        $simrsPatient->alamat_kec_pasien ? 'Kec. ' . $simrsPatient->alamat_kec_pasien : null,
                        $simrsPatient->alamat_kota_pasien ?? null,
                    ])->filter()->implode(', ');

                    // Simpan / update identitas beserta id_pasien SIMRS ke DB Lokal
                    $localUser = User::updateOrCreate(
                        ['no_rkm_medis' => $simrsPatient->no_rm_pasien],
                        [
                            'id_pasien_simrs' => $simrsPatient->id_pasien, // Primary Key SIMRS (contoh: 17497306580ZNOo)
                            'no_ktp'          => $simrsPatient->nik_pasien ?? $rawIdentitas,
                            'name'            => $simrsPatient->nama_pasien ?? 'Pasien',
                            'jk'              => $simrsPatient->id_jns_kelamin ?? null,
                            'tmp_lahir'       => $simrsPatient->lahir_tmpt_pasien ?? null,
                            'tgl_lahir'       => $simrsPatient->lahir_tgl_pasien,
                            'nm_ibu'          => $simrsPatient->nama_ortu_pasien ?? null,
                            'no_tlp'          => $simrsPatient->no_telp_pasien ?? null,
                            'alamat'          => $alamatLengkap ?: '-',
                            'email'           => ($simrsPatient->no_rm_pasien ?: $rawIdentitas) . '@epasien.local',
                        ]
                    );
                }
            }

            // 3. Login Sesi Laravel
            Auth::login($localUser, true);
            $request->session()->regenerate();

            return redirect()->route('dashboard');

        } catch (\Throwable $e) {
            return back()->withErrors([
                'login_error' => 'Koneksi ke database SIMRS gagal: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Registrasi Pasien Baru
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'no_ktp'    => 'required|digits:16|unique:users,no_ktp',
            'name'      => 'required|string|max:100',
            'jk'        => 'required|in:L,P',
            'tmp_lahir' => 'required|string|max:50',
            'tgl_lahir' => 'required|date',
            'nm_ibu'    => 'required|string|max:60',
            'no_tlp'    => 'required|string|max:20',
            'alamat'    => 'required|string',
        ], [
            'no_ktp.required'    => 'NIK KTP wajib diisi.',
            'no_ktp.digits'      => 'NIK KTP harus 16 digit.',
            'no_ktp.unique'      => 'NIK KTP ini sudah terdaftar di sistem lokal.',
            'name.required'      => 'Nama lengkap wajib diisi.',
            'jk.required'        => 'Pilih jenis kelamin.',
            'tmp_lahir.required' => 'Tempat lahir wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'nm_ibu.required'    => 'Nama orang tua/ibu kandung wajib diisi.',
            'no_tlp.required'    => 'Nomor WhatsApp wajib diisi.',
            'alamat.required'    => 'Alamat domisili wajib diisi.',
        ]);

        try {
            // Cek apakah NIK sudah tercatat di SIMRS
            $cekSimrs = DB::connection('simrs')->table('dt_pasien')
                ->where('nik_pasien', $validated['no_ktp'])
                ->first();

            if ($cekSimrs) {
                return back()->withErrors([
                    'register_error' => 'NIK Anda sudah tercatat di SIMRS dengan No. RM: ' . $cekSimrs->no_rm_pasien . '. Silakan login menggunakan No. RM tersebut.',
                ]);
            }

            // Simpan pasien baru ke DB Lokal
            $newUser = User::create([
                'id_pasien_simrs' => null,
                'no_rkm_medis'    => null,
                'no_ktp'          => $validated['no_ktp'],
                'name'            => $validated['name'],
                'jk'              => $validated['jk'],
                'tmp_lahir'       => $validated['tmp_lahir'],
                'tgl_lahir'       => $validated['tgl_lahir'],
                'nm_ibu'          => $validated['nm_ibu'],
                'no_tlp'          => $validated['no_tlp'],
                'alamat'          => $validated['alamat'],
                'email'           => $validated['no_ktp'] . '@epasien.local',
            ]);

            Auth::login($newUser, true);
            $request->session()->regenerate();

            return redirect()->route('dashboard');

        } catch (\Throwable $e) {
            return back()->withErrors([
                'register_error' => 'Gagal memproses pendaftaran: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
