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
        Schema::create('environment_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('power_control_serial_number')->nullable();
            $table->integer('ampere_consumption')->nullable();
            $table->boolean('mini_phase')->default(0);
            $table->boolean('three_phase')->default(0);
            $table->string('power_control_ownership')->nullable();
            $table->integer('fan_quantity')->nullable();
            $table->integer('faulty_fan_quantity')->nullable();
            $table->boolean('earthing_system')->default(0);
            $table->string('air_conditioner_1_type')->nullable();
            $table->string('air_conditioner_2_type')->nullable();
            $table->string('stabilizer_quantity')->nullable();
            $table->string('stabilizer_type')->nullable();
            $table->boolean('exiting')->default(0);
            $table->boolean('working')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('environment_informations');
    }
};
