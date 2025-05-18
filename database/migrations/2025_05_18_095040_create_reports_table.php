<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generator_id')->constrained()->onDelete('cascade');
            $table->foreignId('mtn_site_id')->constrained()->onDelete('cascade');
            $table->enum('visit_type', ['routine', 'emergency', 'overhaul']);
            $table->string('report_number')->unique();
            $table->date('visit_date');
            $table->time('visit_time');
            $table->decimal('current_reading', 10, 2);
            $table->decimal('previous_reading', 10, 2)->nullable();
            $table->boolean('ats_status')->default(true);
            $table->date('previous_visit_date')->nullable();
            $table->decimal('oil_pressure', 5, 2)->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('burned_oil_quantity', 5, 2)->nullable();
            $table->decimal('battery_voltage', 5, 2)->nullable();
            $table->decimal('frequency', 5, 2)->nullable();
            $table->decimal('voltage_L1', 5, 2)->nullable();
            $table->decimal('voltage_L2', 5, 2)->nullable();
            $table->decimal('voltage_L3', 5, 2)->nullable();
            $table->decimal('load_L1', 5, 2)->nullable();
            $table->decimal('load_L2', 5, 2)->nullable();
            $table->decimal('load_L3', 5, 2)->nullable();
            $table->decimal('oil_quantity', 5, 2)->nullable();
            $table->string('visit_reason')->nullable();
            $table->string('technical_status')->nullable();
            $table->index(['generator_id', 'mtn_site_id', 'visit_type'], 'idx_generator_site_visit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('idx_generator_site_visit');
        });

        Schema::dropIfExists('reports');
    }

};
