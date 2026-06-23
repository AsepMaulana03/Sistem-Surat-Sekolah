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
        Schema::create('incoming_letters', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('no_surat');
            $table->string('perihal');
            $table->string('asal_surat');
            $table->string('status')->default('menunggu_disposisi'); // menunggu_disposisi, selesai
            $table->text('instruksi_disposisi')->nullable();
            $table->timestamp('disposisi_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_letters');
    }
};
