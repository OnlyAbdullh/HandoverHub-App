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
        Schema::create('tower_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->boolean('mast')->default(0);
            $table->boolean('tower')->default(0);;
            $table->boolean('monopole')->default(0);;
            $table->string('mast_number');
            $table->string('mast_status');
            $table->string('tower_number');
            $table->string('tower_status');
            $table->string('beacon_status');
            $table->string('monopole_number');
            $table->string('monopole_status');
            $table->string('mast_1_height');
            $table->string('mast_2_height')->nullable();
            $table->string('mast_3_height')->nullable();
            $table->string('tower_1_height')->nullable();
            $table->string('tower_2_height')->nullable();
            $table->string('monopole_height')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tower_informations');
    }
};
