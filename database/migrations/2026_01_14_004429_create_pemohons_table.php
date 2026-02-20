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
        Schema::create('pemohons', function (Blueprint $table) {
                $table->id();

                    $table->string('instansi_pemohon')->index();
                    $table->text('data_diminta');
                    $table->text('tujuan_penggunaan');
                    $table->string('opd_tujuan')->index();
                    $table->string('upload_surat'); // path file PDF/Excel
                    

                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemohons');
    }
};
