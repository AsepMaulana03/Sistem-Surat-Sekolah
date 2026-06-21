<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->text('rejection_note')->nullable()->after('content');
            $table->timestamp('reviewed_at')->nullable()->after('rejection_note');
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['rejection_note', 'reviewed_at', 'reviewed_by']);
        });
    }
};
