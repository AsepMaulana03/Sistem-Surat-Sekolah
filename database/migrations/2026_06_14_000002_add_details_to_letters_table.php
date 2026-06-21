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
        Schema::table('letters', function (Blueprint $table) {
            $table->string('type_code')->after('user_id')->nullable();
            $table->string('letter_number')->after('type_code')->nullable();
            $table->string('event_name')->after('title')->nullable();
            $table->date('letter_date')->after('event_name')->nullable();
            $table->string('destination')->after('letter_date')->nullable();
            $table->text('content')->after('destination')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn(['type_code', 'letter_number', 'event_name', 'letter_date', 'destination', 'content']);
        });
    }
};
