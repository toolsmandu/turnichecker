<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_pack_id')->nullable()->constrained('user_packs')->nullOnDelete();
            $table->string('original_name');
            $table->string('file_path');
            $table->enum('status', ['processing', 'completed'])->default('processing');
            $table->string('similarity_report_path')->nullable();
            $table->string('ai_report_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
