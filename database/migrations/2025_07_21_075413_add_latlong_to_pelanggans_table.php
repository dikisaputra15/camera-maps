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
             $table->decimal('kwh_latitude', 10, 6)->nullable()->after('tanggal_foto');
             $table->decimal('kwh_longitude', 10, 6)->nullable()->after('kwh_latitude');
             $table->boolean('verified')->default(false)->after('kwh_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn('kwh_latitude');
            $table->dropColumn('kwh_longitude');
            $table->dropColumn('verified');
        });
    }
};
