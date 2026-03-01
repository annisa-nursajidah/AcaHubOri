<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        // Add 'school_admin' to the role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','teacher','student','school_admin') DEFAULT 'student'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','teacher','student') DEFAULT 'student'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }
};
