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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tax_model_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tax_model_reminder_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('notification_type');
            $table->timestamp('sent_at');
            $table->text('details')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'sent_at']);
            $table->index(['tax_model_id', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
