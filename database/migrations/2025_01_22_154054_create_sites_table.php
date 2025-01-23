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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('governorate');
            $table->string('street');
            $table->string('area');
            $table->string('city');
            $table->enum('type', ['Outdoor', 'Indoor', 'Micro', 'PTS Shelter', 'Old Shelter']);
            $table->boolean('gsm1900');
            $table->boolean('gsm1800');
            $table->boolean('3g');
            $table->boolean('lte');
            $table->boolean('generator');
            $table->boolean('solar');
            $table->boolean('wind');
            $table->boolean('grid');
            $table->boolean('fence');
            $table->string('cabinet_number');
            $table->string('electricity_meter');
            $table->string('electricity_meter_reading');
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
