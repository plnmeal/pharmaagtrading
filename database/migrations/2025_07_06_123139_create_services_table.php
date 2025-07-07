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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique(); // E.g., "Smart Warehousing"
            $table->string('icon_class')->nullable(); // E.g., "fa-solid fa-boxes-packing"
            $table->text('description')->nullable(); // Detailed description of the service
            $table->integer('order')->default(0); // For custom sorting
            $table->boolean('is_active')->default(true); // To easily hide/show

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};