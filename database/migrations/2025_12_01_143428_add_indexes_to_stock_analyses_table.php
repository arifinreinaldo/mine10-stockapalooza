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
        Schema::table('stock_analyses', function (Blueprint $table) {
            // Add indexes for common query patterns
            $table->index('market'); // Filter by market (IDX, SGX, US)
            $table->index('recommendation'); // Filter by recommendation type
            $table->index('overall_score'); // Sort by score (most common sort)
            $table->index('institutional_percent'); // Institutional stock queries
            $table->index('created_at'); // Time-based queries
            $table->index(['market', 'overall_score']); // Composite for market-specific rankings
            $table->index(['recommendation', 'overall_score']); // Composite for filtering + sorting
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_analyses', function (Blueprint $table) {
            // Drop indexes in reverse order
            $table->dropIndex(['recommendation', 'overall_score']);
            $table->dropIndex(['market', 'overall_score']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['institutional_percent']);
            $table->dropIndex(['overall_score']);
            $table->dropIndex(['recommendation']);
            $table->dropIndex(['market']);
        });
    }
};
