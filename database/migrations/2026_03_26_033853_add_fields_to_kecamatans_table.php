<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kecamatans', function (Blueprint $table) {
            $table->string('website')->nullable();
            $table->string('pembuat')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('kecamatans', function (Blueprint $table) {
            $table->dropColumn(['website', 'pembuat', 'status', 'keterangan']);
        });
    }
};
