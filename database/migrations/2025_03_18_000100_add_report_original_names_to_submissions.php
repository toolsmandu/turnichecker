<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('similarity_report_original_name')->nullable()->after('similarity_report_path');
            $table->string('ai_report_original_name')->nullable()->after('ai_report_path');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['similarity_report_original_name', 'ai_report_original_name']);
        });
    }
};
