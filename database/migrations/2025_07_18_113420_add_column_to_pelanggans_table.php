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
        Schema::table('pelanggans', function (Blueprint $table) {
             $table->string('difoto_oleh')->nullable()->after('gambar_kwh');
             $table->timestamp('tanggal_foto')->nullable()->after('difoto_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn('difoto_oleh');
            $table->dropColumn('tanggal_foto');
        });
    }
};
