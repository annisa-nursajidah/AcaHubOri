<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Peringatan: Karena tabel ini mengubah struktur foreign keys yang sudah ada
        // Anda mungkin perlu me-reset total migrate (fresh) jika di mode development,
        // namun instruksi down/up ini diatur seakan me-replace semuanya.
        Schema::dropIfExists('attendances'); // Drop existing first to reconstruct
        
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_session_id')->nullable()->constrained()->onDelete('set null'); // Nullable for backwards app compatibility if manual attendance used
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete(); // Directly link to users table as student
            
            // Konteks Absensi
            $table->date('date');
            $table->enum('status', ['present', 'late', 'absent', 'excused', 'sick'])->default('present');
            $table->timestamp('scanned_at')->nullable(); // Waktu pasti siswa melakukan scan
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // 1 Siswa hanya boleh absen 1x per sesi
            $table->unique(['attendance_session_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
