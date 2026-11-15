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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('textile_id')->constrained()->restrictOnDelete();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('moq_quantity', total: 12, places: 2)->nullable();
            $table->string('moq_unit')->nullable();
            $table->string('certification')->nullable();
            $table->string('compliance_standard')->nullable();
            $table->text('chemical_info')->nullable();
            $table->json('production_stages')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('textile_id');
            $table->index(['is_published', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
