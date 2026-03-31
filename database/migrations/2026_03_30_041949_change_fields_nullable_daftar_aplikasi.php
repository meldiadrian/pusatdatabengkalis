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
        Schema::table('daftar_aplikasi_perangkat_daerahs', function (Blueprint $table) {
            $table->text('dimanfaatkan_untuk_layanan')->nullable()->change();
            $table->text('spesifikasi_teknis')->nullable()->change();
            $table->string('mode')->nullable()->change();
            $table->boolean('pembuat_diskominfotik')->nullable()->change();
            $table->boolean('jenis_web')->nullable()->change();
            $table->boolean('jenis_mobile')->nullable()->change();
            $table->boolean('pemilik_pusat')->nullable()->change();
            $table->boolean('pemilik_daerah')->nullable()->change();
            $table->text('keterangan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_aplikasi_perangkat_daerahs', function (Blueprint $table) {
            $table->text('dimanfaatkan_untuk_layanan')->nullable(false)->change();
            $table->string('mode')->nullable(false)->change();
            $table->boolean('pembuat_diskominfotik')->nullable(false)->change();
            $table->text('spesifikasi_teknis')->nullable(false)->change();
              $table->boolean('jenis_web')->nullable()(false)->change();
            $table->boolean('jenis_mobile')->nullable()(false)->change();
            $table->boolean('pemilik_pusat')->nullable()(false)->change();
            $table->boolean('pemilik_daerah')->nullable()(false)->change();
            $table->text('keterangan')->nullable()(false)->change();
        });
    }
};
