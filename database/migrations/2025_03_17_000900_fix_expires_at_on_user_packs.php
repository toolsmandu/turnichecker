<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure expires_at is not auto-updated by MySQL; force plain DATETIME.
        DB::statement('ALTER TABLE user_packs MODIFY expires_at DATETIME NOT NULL');
    }

    public function down(): void
    {
        // Revert to TIMESTAMP (no ON UPDATE clause) to match original intent.
        DB::statement('ALTER TABLE user_packs MODIFY expires_at TIMESTAMP NOT NULL');
    }
};
