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
        Schema::create('pemilik_data', function (Blueprint $table) {
            $table->id();
                $table->foreignId('pemohon_id')
                        ->constrained('pemohons')
                        ->cascadeOnDelete();
                $table->string('file_data')->nullable();   // upload PDF / Excel
                $table->text('keterangan')->nullable();    // jika data tidak tersedia

            
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemilik_data');
    }
};
