<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->unsignedBigInteger('submission_number')->nullable()->unique()->after('id');
        });

        // Backfill existing rows with sequential numbers starting at 231.
        $next = 231;
        DB::table('submissions')
            ->orderBy('id')
            ->select('id')
            ->lazyById()
            ->each(function ($row) use (&$next) {
                DB::table('submissions')->where('id', $row->id)->update(['submission_number' => $next]);
                $next++;
            });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropUnique(['submission_number']);
            $table->dropColumn('submission_number');
        });
    }
};
