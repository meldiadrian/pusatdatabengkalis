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
            $table->unsignedBigInteger('unit_kerja_id')->nullable()->after('id');
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerjas')->onDelete('set null');
            $table->dropColumn('perangkat_daerah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_aplikasi_perangkat_daerahs', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropColumn('unit_kerja_id');
            $table->string('perangkat_daerah')->after('id');
        });
    }
};
