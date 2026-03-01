<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->enum('tipe', ['akademik', 'ujian', 'libur', 'lainnya'])->default('akademik');
            $table->string('warna', 7)->default('#0891b2'); // brand-500
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
