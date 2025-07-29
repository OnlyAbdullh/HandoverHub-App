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
        Schema::create('replaced_parts', function (Blueprint $table) {
            $table->foreignId('part_id')->constrained()->onDelete('cascade');
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->integer('faulty_quantity')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_faulty')->default(false);
            $table->primary(['part_id', 'report_id']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replaced_parts');
    }
};
