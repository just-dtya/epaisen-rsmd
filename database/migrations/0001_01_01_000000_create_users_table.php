<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Kolom Identitas Pasien SIMRS
            $table->string('id_pasien_simrs', 50)->nullable()->unique()->after('id')->comment('PK id_pasien dari dt_pasien SIMRS');
            $table->string('no_rkm_medis', 20)->unique()->nullable()->comment('No. Rekam Medis Pasien');
            $table->string('no_ktp', 25)->nullable()->index()->comment('NIK KTP Pasien');
            $table->string('name')->comment('Nama Lengkap Pasien');
            $table->enum('jk', ['L', 'P'])->nullable()->comment('Jenis Kelamin: L/P');
            $table->string('tmp_lahir', 50)->nullable()->comment('Tempat Lahir');
            $table->date('tgl_lahir')->nullable()->index()->comment('Tanggal Lahir Pasien');
            $table->string('nm_ibu', 60)->nullable()->comment('Nama Ibu Kandung');
            $table->string('no_tlp', 30)->nullable()->comment('Nomor WhatsApp / HP');
            $table->text('alamat')->nullable()->comment('Alamat Lengkap Domisili');

            // Kolom Autentikasi Standar Laravel (Nullable untuk login No RM + Tgl Lahir)
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
