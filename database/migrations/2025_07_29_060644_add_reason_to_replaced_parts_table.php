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
            $table->enum('reason', ['بدل مسروق', 'بدل عاطل', 'إضافة', 'لا يوجد عاطل'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('replaced_parts', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};
