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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->default('Unknown');
            $table->string('name')->nullable();
            $table->string('code')->unique()->nullable();
            $table->string('governorate')->nullable();
            $table->string('street')->nullable();
            $table->string('area')->nullable();
            $table->string('city')->nullable();
            $table->enum('type', ['Outdoor', 'Indoor', 'Micro', 'PTS Shelter', 'Old Shelter'])->nullable();
            $table->boolean('gsm1900')->nullable();
            $table->boolean('gsm1800')->nullable();
            $table->boolean('3g')->nullable();
            $table->boolean('lte')->nullable();
            $table->boolean('generator')->nullable();
            $table->boolean('solar')->nullable();
            $table->boolean('wind')->nullable();
            $table->boolean('grid')->nullable();
            $table->boolean('fence')->nullable();
            $table->integer('cabinet_number')->nullable();
            $table->string('electricity_meter')->nullable();
            $table->string('electricity_meter_reading')->nullable();
            $table->text('generator_remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
