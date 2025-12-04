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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('TurnitDetect');
            $table->string('document_count')->default('46,753,647+');
            $table->string('document_label')->default('Documents Checked');
            $table->string('live_label')->default('Live Count');
            $table->string('hero_title')->default('TurnitDetect');
            $table->text('hero_subtitle')->nullable();
            $table->json('feature_tags')->nullable();
            $table->string('button_text')->default('Get Started Free');
            $table->string('button_link')->default('#');
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
