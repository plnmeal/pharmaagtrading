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
        Schema::table('products', function (Blueprint $table) {
            // Check if column exists before dropping to prevent errors if already dropped
            if (Schema::hasColumn('products', 'product_image_path')) {
                $table->dropColumn('product_image_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // If rolling back, re-add the column. It will be added after availability_status.
            $table->string('product_image_path')->nullable()->after('availability_status');
        });
    }
};