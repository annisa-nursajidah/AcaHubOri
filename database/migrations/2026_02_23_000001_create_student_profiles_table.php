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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nis')->unique()->nullable()->comment('Nomor Induk Siswa');
            $table->string('kelas')->nullable()->comment('Kelas / Grade Level');
            $table->date('tanggal_lahir')->nullable()->comment('Tanggal Lahir');
            $table->text('alamat')->nullable()->comment('Alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
