<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('packs')->update(['duration_days' => 7]);
    }

    public function down(): void
    {
        DB::table('packs')->update(['duration_days' => 30]);
    }
};
