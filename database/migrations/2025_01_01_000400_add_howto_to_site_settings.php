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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('howto_title')->nullable()->after('faqs');
            $table->json('howto_steps')->nullable()->after('howto_title');
            $table->string('howto_button_text')->nullable()->after('howto_steps');
            $table->string('howto_button_link')->nullable()->after('howto_button_text');
            $table->string('howto_video_text')->nullable()->after('howto_button_link');
            $table->string('howto_image_path')->nullable()->after('howto_video_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'howto_title',
                'howto_steps',
                'howto_button_text',
                'howto_button_link',
                'howto_video_text',
                'howto_image_path',
            ]);
        });
    }
};
