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
        Schema::table('deadlines', function (Blueprint $table) {
            $table->date('period_start')->nullable()->after('deadline_time');
            $table->date('period_end')->nullable()->after('period_start');
            $table->integer('days_to_complete')->nullable()->after('period_end');
            $table->string('period_description')->nullable()->after('days_to_complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deadlines', function (Blueprint $table) {
            $table->dropColumn(['period_start', 'period_end', 'days_to_complete', 'period_description']);
        });
    }
};
