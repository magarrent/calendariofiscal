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
        Schema::create('tax_models', function (Blueprint $table) {
            $table->id();
            $table->string('model_number')->unique(); // e.g., "303", "130"
            $table->string('name'); // e.g., "IVA - Autoliquidación"
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->text('penalties')->nullable();
            $table->enum('frequency', ['monthly', 'quarterly', 'annual', 'one-time'])->default('monthly');
            $table->json('applicable_to')->nullable(); // ['autonomo', 'pyme', 'large_corp']
            $table->string('aeat_url')->nullable();
            $table->string('category')->nullable(); // 'iva', 'irpf', 'retenciones', etc.
            $table->integer('year')->default(2026);
            $table->timestamps();

            $table->index(['category', 'frequency']);
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_models');
    }
};
