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
            $table->string('solar_type');
            $table->string('solar_capacity');
            $table->string('number_of_panels');
            $table->string('number_of_modules');
            $table->string('number_of_faulty_modules');
            $table->string('number_of_batteries');
            $table->string('battery_type');
            $table->string('battery_status')->comment('1=Bad, 2=Good, 4=Very Good');
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
