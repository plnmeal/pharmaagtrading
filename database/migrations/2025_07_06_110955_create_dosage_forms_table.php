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
        Schema::create('dosage_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // E.g., "Injectables", "Tablets & Capsules"
            $table->string('icon_class')->nullable(); // E.g., "fa-syringe", "fa-pills"
            $table->text('description')->nullable(); // Short description for the homepage
            $table->integer('order')->default(0); // For custom sorting
            $table->boolean('is_active')->default(true); // To easily hide/show
            $table->boolean('is_featured')->default(false); // To show on homepage portfolio section

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
        Schema::dropIfExists('dosage_forms');
    }
};