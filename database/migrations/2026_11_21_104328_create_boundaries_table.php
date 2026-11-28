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
        Schema::create('boundaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['scheme_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boundaries');
    }
};
