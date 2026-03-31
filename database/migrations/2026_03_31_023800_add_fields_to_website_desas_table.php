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
        Schema::table('website_desas', function (Blueprint $table) {
            $table->string('pembuat')->nullable()->after('websitedesa');
            $table->string('status')->nullable()->after('pembuat');
            $table->text('keterangan')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_desas', function (Blueprint $table) {
            $table->dropColumn([
                'pembuat',
                'status',
                'keterangan'
            ]);
        });
    }
};
