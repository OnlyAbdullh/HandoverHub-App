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
            $table->string('solar_type');
            $table->integer('solar_capacity');
            $table->integer('number_of_panels');
            $table->integer('number_of_modules');
            $table->integer('number_of_faulty_modules');
            $table->integer('number_of_batteries');
            $table->string('battery_type');
            $table->integer('battery_status')->comment('1=Bad, 2=Good, 4=Very Good');
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
