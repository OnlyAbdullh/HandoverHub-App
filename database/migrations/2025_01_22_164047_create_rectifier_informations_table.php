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
        Schema::create('rectifier_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->string('rectifier_1_type_and_voltage')->nullable();
            $table->string('rectifier_2_type_and_voltage')->nullable();
            $table->string('module_1_quantity')->nullable();
            $table->string('module_2_quantity')->nullable();
            $table->string('faulty_module_1_quantity')->nullable();
            $table->string('faulty_module_2_quantity')->nullable();
            $table->string('number_of_batteries')->nullable();
            $table->string('battery_type')->nullable();
            $table->string('batteries_cabinet_type')->nullable();
            $table->boolean('cabinet_cage')->default(0)->nullable();
            $table->string('batteries_status')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rectifier_informations');
    }
};
