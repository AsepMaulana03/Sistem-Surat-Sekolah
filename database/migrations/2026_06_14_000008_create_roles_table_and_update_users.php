<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        // 2. Insert default roles
        DB::table('roles')->insert([
            ['name' => 'Staff TU', 'code' => 'tu', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kepala Sekolah', 'code' => 'kepsek', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $tuRole = DB::table('roles')->where('code', 'tu')->first();
        $kepsekRole = DB::table('roles')->where('code', 'kepsek')->first();

        // 3. Update users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete()->after('password');
        });

        // 4. Migrate data
        DB::table('users')->where('role', 'tu')->update(['role_id' => $tuRole->id]);
        DB::table('users')->where('role', 'kepsek')->update(['role_id' => $kepsekRole->id]);

        // If any user didn't have a role matched, set them to TU by default
        DB::table('users')->whereNull('role_id')->update(['role_id' => $tuRole->id]);

        // Make role_id non-nullable if desired, but nullable is fine for now

        // 5. Drop old role column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('tu')->after('role_id');
        });

        $tuRole = DB::table('roles')->where('code', 'tu')->first();
        $kepsekRole = DB::table('roles')->where('code', 'kepsek')->first();

        if ($tuRole) {
            DB::table('users')->where('role_id', $tuRole->id)->update(['role' => 'tu']);
        }
        if ($kepsekRole) {
            DB::table('users')->where('role_id', $kepsekRole->id)->update(['role' => 'kepsek']);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
