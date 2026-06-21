<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
            $table->text('content')->nullable()->change();
        });

        // Seed initial templates
        $initialTemplates = [
            ['code' => '01', 'name' => 'Surat Keputusan (SK)'],
            ['code' => '02', 'name' => 'Surat Undangan (SU)'],
            ['code' => '03', 'name' => 'Surat Permohonan (SPm)'],
            ['code' => '04', 'name' => 'Surat Pemberitahuan (SPb)'],
            ['code' => '05', 'name' => 'Surat Peminjaman (SPp)'],
            ['code' => '06', 'name' => 'Surat Pernyataan (SPn)'],
            ['code' => '07', 'name' => 'Surat Mandat (SM)'],
            ['code' => '08', 'name' => 'Surat Tugas (ST)'],
            ['code' => '09', 'name' => 'Surat Keterangan (SKet)'],
            ['code' => '10', 'name' => 'Surat Rekomendasi (SR)'],
            ['code' => '11', 'name' => 'Surat Balasan (SB)'],
            ['code' => '12', 'name' => 'Surat Perintah Perjalanan Dinas (SPPD)'],
            ['code' => '13', 'name' => 'Sertifikat (SRT)'],
            ['code' => '14', 'name' => 'Perjanjian Kerja (PK)'],
            ['code' => '15', 'name' => 'Surat Pengantar (SPeng)'],
        ];

        foreach ($initialTemplates as $template) {
            DB::table('templates')->updateOrInsert(
                ['name' => $template['name']],
                ['code' => $template['code'], 'content' => '', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->text('content')->nullable(false)->change();
        });
    }
};
