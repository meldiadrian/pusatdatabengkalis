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
        Schema::create('daftar_aplikasi_perangkat_daerahs', function (Blueprint $table) {
            $table->id();
            $table->string('perangkat_daerah');
            $table->string('nama_aplikasi');

            // Sifat
            $table->enum('mode', ['online', 'offline']);

            $table->string('alamat_domain')->nullable();
            $table->year('tahun_penganggaran');

            $table->text('dimanfaatkan_untuk_layanan');

            // Pembuat
            $table->string('pembuat_pihak_ketiga')->nullable();
            $table->boolean('pembuat_diskominfotik')->default(false);

            $table->text('spesifikasi_teknis');

            // Jenis Aplikasi
            $table->boolean('jenis_web')->default(false);
            $table->boolean('jenis_mobile')->default(false);

            // Pemilik Aplikasi
            $table->boolean('pemilik_pusat')->default(false);
            $table->boolean('pemilik_daerah')->default(false);

            // Status
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');

            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_aplikasi_perangkat_daerahs');
    }
};
