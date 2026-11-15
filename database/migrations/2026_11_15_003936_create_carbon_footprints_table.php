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
        Schema::create('carbon_footprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('textile_id')->constrained()->cascadeOnDelete();
            $table->decimal('kg_co2e_per_kg', total: 10, places: 4);
            $table->string('system_boundary')->default('cradle_to_gate');
            $table->enum('data_status', ['placeholder', 'estimated', 'measured', 'verified'])->default('placeholder');
            $table->text('source_citation')->nullable();
            $table->decimal('water_l_per_kg', total: 10, places: 2)->nullable();
            $table->string('energy_note')->nullable();
            $table->decimal('sustainability_score', total: 3, places: 1)->nullable();
            $table->enum('recyclability', ['low', 'medium', 'high'])->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique('textile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carbon_footprints');
    }
};
