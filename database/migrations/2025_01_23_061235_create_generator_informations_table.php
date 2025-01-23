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
        Schema::create('generator_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('gen_type_and_capacity');
            $table->string('gen_hour_meter');
            $table->string('gen_fuel_consumption');
            $table->string('internal_capacity');
            $table->string('internal_existing_fuel');
            $table->boolean('internal_cage')->default(0);
            $table->string('external_capacity');
            $table->string('external_existing_fuel');
            $table->boolean('external_cage')->default(0);
            $table->boolean('fuel_sensor_exiting')->default(0);
            $table->boolean('fuel_sensor_working')->default(0);
            $table->string('fuel_sensor_type');
            $table->string('ampere_to_owner');
            $table->string('circuit_breakers_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generator_informations');
    }
};
