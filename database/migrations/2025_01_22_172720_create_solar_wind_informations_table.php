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
        Schema::create('solar_wind_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('solar_type')->nullable();
            $table->string('solar_capacity')->nullable();
            $table->string('number_of_panels')->nullable();
            $table->string('number_of_modules')->nullable();
            $table->string('number_of_faulty_modules')->nullable();
            $table->string('number_of_batteries')->nullable();
            $table->string('battery_type')->nullable();
            $table->string('battery_status')->nullable();
            $table->text('wind_remarks')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solar_wind_informations');
    }
};
