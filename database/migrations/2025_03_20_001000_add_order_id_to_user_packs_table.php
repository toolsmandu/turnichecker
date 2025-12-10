<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_packs', function (Blueprint $table) {
            $table->string('order_id', 40)->nullable()->after('id');
        });

        // Backfill existing records with sequential order ids.
        $counter = $this->currentMaxOrderNumber();

        DB::table('user_packs')
            ->select('id')
            ->whereNull('order_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->chunkById(100, function ($userPacks) use (&$counter) {
                foreach ($userPacks as $userPack) {
                    $counter++;
                    DB::table('user_packs')
                        ->where('id', $userPack->id)
                        ->update([
                            'order_id' => 'ORD-'.$counter,
                            'updated_at' => now(),
                        ]);
                }
            });

        // Enforce uniqueness and non-null constraint after backfill.
        Schema::table('user_packs', function (Blueprint $table) {
            $table->unique('order_id');
        });

        DB::statement('ALTER TABLE user_packs MODIFY order_id VARCHAR(40) NOT NULL');
    }

    public function down(): void
    {
        Schema::table('user_packs', function (Blueprint $table) {
            $table->dropUnique('user_packs_order_id_unique');
            $table->dropColumn('order_id');
        });
    }

    private function currentMaxOrderNumber(): int
    {
        $orderIds = DB::table('user_packs')->pluck('order_id');
        $max = 0;

        foreach ($orderIds as $orderId) {
            if ($orderId && preg_match('/^ORD-(\d+)$/', $orderId, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return $max;
    }
};
