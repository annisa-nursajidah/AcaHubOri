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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained('student_profiles')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_profile_id')->nullable()->constrained('teacher_profiles')->nullOnDelete();
            $table->decimal('nilai', 5, 2)->comment('Nilai / Score');
            $table->enum('tipe', ['tugas', 'uts', 'uas', 'praktik'])->comment('Tipe Penilaian');
            $table->string('semester')->comment('Semester');
            $table->string('tahun_ajaran')->comment('Tahun Ajaran');
            $table->text('catatan')->nullable()->comment('Catatan');
            $table->timestamps();

            $table->index(['student_profile_id', 'subject_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
