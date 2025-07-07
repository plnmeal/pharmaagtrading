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
        $table->string('name')->unique();
        $table->string('slug')->unique();

        // Relationships (Foreign Keys)
        $table->foreignId('manufacturer_id')->nullable()->constrained('manufacturers')->onDelete('set null');
        $table->foreignId('dosage_form_id')->nullable()->constrained('dosage_forms')->onDelete('set null');
        // NEW: Foreign key for Therapeutic Category
        $table->foreignId('therapeutic_category_id')->nullable()->constrained('therapeutic_categories')->onDelete('set null');

        // Removed: $table->string('therapeutic_category')->nullable(); // This line is gone!

        $table->text('description')->nullable();
        $table->longText('benefits')->nullable();

        $table->string('availability_status')->default('Available');
        $table->string('product_image_path')->nullable();

        $table->integer('order')->default(0);
        $table->boolean('is_active')->default(true);

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
        Schema::dropIfExists('products');
    }
};