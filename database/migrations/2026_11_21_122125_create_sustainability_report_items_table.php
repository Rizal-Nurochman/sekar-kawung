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
        Schema::create('sustainability_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sustainability_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained()->restrictOnDelete();
            $table->foreignId('option_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('quantity', total: 12, places: 4)->default(0);
            $table->decimal('emission_result', total: 20, places: 10)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sustainability_report_items');
    }
};
