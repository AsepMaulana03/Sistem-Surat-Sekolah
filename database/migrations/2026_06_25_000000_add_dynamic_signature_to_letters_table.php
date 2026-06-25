<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modify enum status in letters table
        // Laravel's change() for ENUM often requires doctrine/dbal, so raw statement is safer.
        DB::statement("ALTER TABLE letters MODIFY status ENUM('draft', 'pending', 'menunggu_persetujuan_pihak1', 'approved', 'rejected') DEFAULT 'pending'");

        // 2. Add new columns to letters table
        Schema::table('letters', function (Blueprint $table) {
            $table->integer('jumlah_ttd')->default(1)->after('title');
            $table->foreignId('pihak1_id')->nullable()->constrained('users')->nullOnDelete()->after('user_id');
        });

        // 3. Insert Role 'Guru Pendamping'
        DB::table('roles')->insert([
            'name' => 'Guru Pendamping',
            'code' => 'guru',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $guruRole = DB::table('roles')->where('code', 'guru')->first();

        // 4. Create a default Guru Pendamping user for testing
        DB::table('users')->updateOrInsert(
            ['email' => 'guru@gmail.com'],
            [
                'name' => 'Guru Pendamping',
                'password' => Hash::make('almanshur123'),
                'role_id' => $guruRole->id,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    public function down(): void
    {
        // 1. Revert Enum
        DB::statement("ALTER TABLE letters MODIFY status ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'pending'");

        // 2. Drop columns
        Schema::table('letters', function (Blueprint $table) {
            $table->dropForeign(['pihak1_id']);
            $table->dropColumn(['jumlah_ttd', 'pihak1_id']);
        });

        // 3. Remove user and role
        DB::table('users')->where('email', 'guru@gmail.com')->delete();
        DB::table('roles')->where('code', 'guru')->delete();
    }
};
