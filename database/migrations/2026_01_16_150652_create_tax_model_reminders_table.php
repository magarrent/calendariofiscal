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
        Schema::create('tax_model_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tax_model_id')->constrained()->cascadeOnDelete();
            $table->integer('days_before');
            $table->boolean('enabled')->default(true);
            $table->string('notification_type')->default('email');
            $table->timestamps();

            $table->index(['user_id', 'tax_model_id', 'enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_model_reminders');
    }
};
