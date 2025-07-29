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
              $table->string('nama')->nullable()->after('kwh_longitude');
              $table->string('tarif')->nullable()->after('nama');
              $table->string('daya')->nullable()->after('tarif');
              $table->string('jenis_layanan')->nullable()->after('daya');
              $table->text('alamat')->nullable()->after('jenis_layanan');
              $table->string('rt')->nullable()->after('alamat');
              $table->string('rw')->nullable()->after('rt');
              $table->text('hasil_kunjungan')->nullable()->after('rw');
              $table->string('telp')->nullable()->after('hasil_kunjungan');
              $table->string('kabel_sl')->nullable()->after('telp');
              $table->string('jenis_sambungan')->nullable()->after('kabel_sl');
              $table->string('merk_mcb')->nullable()->after('jenis_sambungan');
              $table->string('ampere_mcb')->nullable()->after('merk_mcb');
              $table->string('gardu')->nullable()->after('ampere_mcb');
              $table->text('gambar_sr')->nullable()->after('gardu');
              $table->text('gambar_tiang')->nullable()->after('gambar_sr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
             $table->dropColumn('nama');
             $table->dropColumn('tarif');
             $table->dropColumn('daya');
             $table->dropColumn('jenis_layanan');
             $table->dropColumn('alamat');
             $table->dropColumn('rt');
             $table->dropColumn('rw');
             $table->dropColumn('hasil_kunjungan');
             $table->dropColumn('telp');
             $table->dropColumn('kabel_sl');
             $table->dropColumn('jenis_sambungan');
             $table->dropColumn('merk_mcb');
             $table->dropColumn('ampere_mcb');
             $table->dropColumn('gardu');
             $table->dropColumn('gambar_sr');
             $table->dropColumn('gambar_tiang');
        });
    }
};
