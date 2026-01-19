<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tax_models', function (Blueprint $table) {
            // Drop the existing unique constraint on model_number
            $table->dropUnique(['model_number']);

            // Add a composite unique constraint on model_number, category, frequency, and year
            $table->unique(['model_number', 'category', 'frequency', 'year'], 'tax_models_unique_identifier');
        });
    }

    public function down(): void
    {
        Schema::table('tax_models', function (Blueprint $table) {
            // Remove the composite unique constraint
            $table->dropUnique('tax_models_unique_identifier');

            // Restore the original unique constraint
            $table->unique('model_number');
        });
    }
};
