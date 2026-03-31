<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_website_desas', function (Blueprint $table) {
            $table->id(); // WAJIB

            $table->foreignId('kecamatan_id')->nullable()->constrained()->nullOnDelete();
            $table->json('desa_ids')->nullable();
            $table->string('website')->nullable();
            $table->string('pembuat')->nullable();
            $table->string('status')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps(); // WAJIB
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_website_desas');
    }
};
