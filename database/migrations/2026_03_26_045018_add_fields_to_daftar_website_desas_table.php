<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('daftar_website_desas', function (Blueprint $table) {
            $table->string('pembuat')->nullable();
            $table->string('status')->nullable();
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('daftar_website_desas', function (Blueprint $table) {
            $table->dropColumn(['pembuat', 'status', 'keterangan']);
        });
    }
};
