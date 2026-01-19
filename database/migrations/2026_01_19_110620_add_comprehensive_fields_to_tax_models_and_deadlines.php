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
        // Add new fields to tax_models table
        Schema::table('tax_models', function (Blueprint $table) {
            $table->text('group_description')->nullable()->after('description');
            $table->string('source_document')->nullable()->after('year');
        });

        // Add new fields to deadlines table (period_start, period_end, days_to_complete, period_description already exist)
        Schema::table('deadlines', function (Blueprint $table) {
            $table->string('uid')->nullable()->after('id')->unique();
            $table->string('period')->nullable()->after('deadline_time');
            $table->string('deadline_label')->nullable()->after('period_end');
            $table->timestamp('check_day_1')->nullable()->after('deadline_label');
            $table->timestamp('check_day_10')->nullable()->after('check_day_1');
            $table->date('rule_start_date')->nullable()->after('check_day_10');
            $table->boolean('is_variable')->default(false)->after('rule_start_date');
            $table->integer('page_number')->nullable()->after('is_variable');
            $table->text('details')->nullable()->after('page_number');
            $table->string('deadline_scope')->nullable()->after('details');
            $table->text('conditions')->nullable()->after('deadline_scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_models', function (Blueprint $table) {
            $table->dropColumn(['group_description', 'source_document']);
        });

        Schema::table('deadlines', function (Blueprint $table) {
            $table->dropColumn([
                'uid',
                'period',
                'deadline_label',
                'check_day_1',
                'check_day_10',
                'rule_start_date',
                'is_variable',
                'page_number',
                'details',
                'deadline_scope',
                'conditions',
            ]);
        });
    }
};
