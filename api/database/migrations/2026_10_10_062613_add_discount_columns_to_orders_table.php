<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('subtotal_cents')->default(0);
            $table->unsignedBigInteger('discount_cents')->default(0);
            $table->foreignId('discount_tier_id')->nullable()->constrained()->nullOnDelete();
            $table->index('discount_tier_id');
        });

        // Les commandes existantes n'avaient pas de remise : sous-total = total.
        DB::table('orders')->update(['subtotal_cents' => DB::raw('total_cents')]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['discount_tier_id']);
            $table->dropIndex(['discount_tier_id']);
            $table->dropColumn(['subtotal_cents', 'discount_cents', 'discount_tier_id']);
        });
    }
};
