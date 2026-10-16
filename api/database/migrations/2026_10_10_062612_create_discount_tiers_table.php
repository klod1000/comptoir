<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('label', 100);
            // L'index unique sert aussi à la recherche du palier applicable.
            $table->unsignedBigInteger('min_subtotal_cents')->unique();
            $table->decimal('rate_percent', 5, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_tiers');
    }
};
