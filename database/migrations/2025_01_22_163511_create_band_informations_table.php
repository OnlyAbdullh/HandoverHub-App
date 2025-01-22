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
        Schema::create('band_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->enum('band_type', ['GSM 900', 'GSM 1800', '3G', 'LTE']);
            $table->string('rbs_1_type')->nullable();
            $table->string('rbs_2_type')->nullable();
            $table->string('du_1_type')->nullable();
            $table->string('du_2_type')->nullable();
            $table->string('ru_1_type')->nullable();
            $table->string('ru_2_type')->nullable();
            $table->text('gsm_900_remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('band_informations');
    }
};
