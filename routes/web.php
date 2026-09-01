<?php

use App\Http\Controllers\Auth\PatientAuthController;
use App\Http\Controllers\BedMonitoringController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\HistoryKunjunganController;
use App\Http\Controllers\JadwalDokterController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\TarifPelayananController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// --- Guest / Autentikasi ---
Route::middleware('guest')->group(function () {
    Route::get('/', [PatientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [PatientAuthController::class, 'login'])->name('login.post');
    Route::get('/daftar', [PatientAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/daftar', [PatientAuthController::class, 'register'])->name('register.post');
});

// --- Pasien Terautentikasi ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [PatientAuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();

        return Inertia::render('HomeDashboard', [
            'patient' => $user ? [
                'id' => $user->id,
                'no_rkm_medis' => $user->no_rkm_medis ?? null,
                'nik' => $user->no_ktp ?? null,
                'nama' => $user->name,
                'jk' => $user->jk ?? 'L',
                'tmp_lahir' => $user->tmp_lahir ?? '-',
                'tgl_lahir' => $user->tgl_lahir ? date('d-m-Y', strtotime($user->tgl_lahir)) : '-',
                'no_tlp' => $user->no_tlp ?? '-',
                'alamat' => $user->alamat ?? '-',
                'nm_ibu' => $user->nm_ibu ?? '-',
            ] : null,
            'berita' => array_slice(BeritaController::getCachedPosts(), 0, 5),
        ]);
    })->name('dashboard');

    Route::get('/riwayat', [HistoryKunjunganController::class, 'index'])->name('riwayat.kunjungan');
    Route::get('/tarif', [TarifPelayananController::class, 'index'])->name('tarif.pelayanan');
    Route::get('/bed-monitoring', [BedMonitoringController::class, 'index'])->name('bed.monitoring');
    Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
    Route::get('/jadwal-dokter', [JadwalDokterController::class, 'index'])->name('jadwal.dokter');

    // Ubah prefix dari 'rekam-medis-pasien' menjadi 'rekam-medis'
    Route::middleware(['auth'])->prefix('rekam-medis')->controller(RekamMedisController::class)->group(function () {
        // 1. Rute List / Index (Tanpa Parameter ID)
        Route::get('/lab', 'indexLab')->name('lab.index');
        Route::get('/radiologi', 'indexRadiologi')->name('radiologi.index');

        // 2. Rute Detail Spesifik Penunjang
        Route::get('/lab/{id_pendaftaran}', 'showLab')->name('lab.show');
        Route::get('/radiologi/{id_pendaftaran}', 'showRadiologi')->name('radiologi.show');

        // 3. Rute Dinamis SOAP Utama (WAJIB DITARUH PALING BAWAH)
        Route::get('/{id_pendaftaran}', 'show')->name('rekam-medis.show');
    });

    Route::get('/rsmd', function () {
        return Inertia::render('LiatRSMDView');
    })->name('rsmd.view');

    Route::get('/pendaftaran', function () {
        return Inertia::render('HomeDashboard');
    })->name('pendaftaran');

    Route::get('/antrean', function () {
        return Inertia::render('HomeDashboard');
    })->name('antrean');

    Route::get('/farmasi', function () {
        return Inertia::render('HomeDashboard');
    })->name('farmasi');
});
