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
        Schema::create('pejabats', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::table('letters', function (Blueprint $table) {
            $table->foreignId('kepsek_id')->nullable()->constrained('pejabats')->nullOnDelete()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropForeign(['kepsek_id']);
            $table->dropColumn('kepsek_id');
        });
        
        Schema::dropIfExists('pejabats');
    }
};
