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
            $table->string('nama_aplikasi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_aplikasi_perangkat_daerahs', function (Blueprint $table) {
            $table->string('nama_aplikasi')->nullable(false)->change();
        });
    }
};
