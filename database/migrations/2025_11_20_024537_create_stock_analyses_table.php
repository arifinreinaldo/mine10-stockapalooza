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
        Schema::create('stock_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 20)->index();
            $table->string('market', 10)->default('idx');
            $table->decimal('price', 15, 2);

            // Technical Indicators
            $table->decimal('rsi', 8, 2)->nullable();
            $table->boolean('above_sma')->nullable();
            $table->decimal('volume_ratio', 8, 2)->nullable();
            $table->string('volatility_rating', 20)->nullable();

            // Fundamentals
            $table->decimal('pe_ratio', 10, 2)->nullable();
            $table->decimal('pb_ratio', 10, 2)->nullable();
            $table->decimal('roe', 8, 2)->nullable();
            $table->decimal('eps', 10, 2)->nullable();

            // Accumulation
            $table->string('accumulation_phase', 30)->nullable();
            $table->integer('accumulation_strength')->nullable();
            $table->integer('accumulation_days')->nullable();
            $table->string('participant_type', 50)->nullable();
            $table->decimal('institutional_percent', 5, 2)->nullable();

            // Swing
            $table->integer('swing_score')->nullable();
            $table->decimal('avg_swing_percent', 8, 2)->nullable();
            $table->string('trend_pattern', 50)->nullable();

            // Overall
            $table->decimal('overall_score', 5, 2);
            $table->string('recommendation', 50);

            // Future price (for ML training - filled later)
            $table->decimal('price_after_7days', 15, 2)->nullable();
            $table->decimal('price_change_7days_percent', 8, 2)->nullable();
            $table->boolean('went_up_5pct')->nullable();

            $table->timestamps();

            // Indexes for ML queries
            $table->index(['symbol', 'created_at']);
            $table->index('went_up_5pct');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_analyses');
    }
};
