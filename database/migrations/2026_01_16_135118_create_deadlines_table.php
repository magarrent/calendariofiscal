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
        Schema::create('deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_model_id')->constrained()->onDelete('cascade');
            $table->date('deadline_date');
            $table->time('deadline_time')->nullable();
            $table->integer('year')->default(2026);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['deadline_date', 'year']);
            $table->index('tax_model_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deadlines');
    }
};
