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
        $tables = ['subjects', 'classrooms', 'academic_years', 'announcements', 'events'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableBlueprint) {
                // Tambahkan setelah ID atau column pertama yang make sense
                $tableBlueprint->foreignId('school_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenanted_tables', function (Blueprint $table) {
            //
        });
    }
};
