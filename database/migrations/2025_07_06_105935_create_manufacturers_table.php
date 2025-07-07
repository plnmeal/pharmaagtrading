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
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Manufacturer name, should be unique
            $table->text('description')->nullable(); // Description of the manufacturer
            $table->string('logo_path')->nullable(); // Path to the manufacturer's logo image
            $table->string('website_url')->nullable(); // Optional: Link to manufacturer's website
            $table->integer('order')->default(0); // For custom sorting
            $table->boolean('is_active')->default(true); // To easily hide/show
            $table->boolean('is_featured')->default(false); // To show on homepage

            // Audit fields (who created/updated the record)
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturers');
    }
};