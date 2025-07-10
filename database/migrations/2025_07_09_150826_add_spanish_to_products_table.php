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
            $table->string('name_es')->nullable()->after('name');
            $table->text('description_es')->nullable()->after('description');
            $table->longText('benefits_es')->nullable()->after('benefits'); // longText for rich editor content
            $table->text('ingredients_es')->nullable()->after('ingredients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name_es', 'description_es', 'benefits_es', 'ingredients_es']);
        });
    }
};