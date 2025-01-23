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
            $table->string('rectifier_1_type_and_voltage');
            $table->string('rectifier_2_type_and_voltage');
            $table->string('module_1_quantity');
            $table->string('module_2_quantity');
            $table->string('faulty_module_1_quantity');
            $table->string('faulty_module_2_quantity');
            $table->string('number_of_batteries');
            $table->string('battery_type');
            $table->string('batteries_cabinet_type');
            $table->boolean('cabinet_cage')->default(0);
            $table->integer('batteries_status')->comment('1=Bad, 2=Good, 4=Very Good');
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
