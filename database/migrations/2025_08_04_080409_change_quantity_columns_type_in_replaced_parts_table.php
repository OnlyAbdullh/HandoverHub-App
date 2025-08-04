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
        Schema::table('replaced_parts', function (Blueprint $table) {
            $table->decimal('quantity', 8, 2)->default(1)->change();
            $table->decimal('faulty_quantity', 8, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('replaced_parts', function (Blueprint $table) {
            $table->integer('quantity')->default(1)->change();
            $table->integer('faulty_quantity')->default(0)->change();
        });
    }
};
