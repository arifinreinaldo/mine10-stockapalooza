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
        Schema::create('analysis_history', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 20)->index();
            $table->string('market', 10)->default('idx');
            $table->string('stock_name', 255)->nullable();
            $table->decimal('price_at_analysis', 15, 2);

            // Phase information captured at analysis time
            $table->string('phase', 30);  // MARKUP, MARKDOWN, DISTRIBUTION, ACCUMULATION, NEUTRAL
            $table->string('phase_confidence', 20)->nullable();  // Strong, Moderate, Weak
            $table->integer('accumulation_strength')->nullable();

            // Key metrics snapshot
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->string('recommendation', 50)->nullable();
            $table->decimal('institutional_percent', 5, 2)->nullable();
            $table->decimal('rsi', 8, 2)->nullable();

            $table->timestamps();

            // Indexes for efficient queries
            $table->index(['symbol', 'created_at']);
            $table->index(['phase', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_history');
    }
};
