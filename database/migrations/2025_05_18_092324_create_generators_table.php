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
        Schema::create('generators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engine_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');

            $table->foreignId('mtn_site_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('initial_meter')->default(0);
            $table->unique(['engine_id', 'brand_id'], 'generators_engine_brand_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('generators', function (Blueprint $table) {
            $table->dropUnique('generators_engine_brand_unique');
        });
        Schema::dropIfExists('generators');
    }
};
