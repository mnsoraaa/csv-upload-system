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
            $table->string('unique_key')->unique();
            $table->string('product_title', 500)->nullable();
            $table->text('product_description')->nullable();
            $table->string('style_number', 100)->nullable();
            $table->string('sanmar_mainframe_color', 100)->nullable();
            $table->string('size', 50)->nullable();
            $table->string('color_name', 100)->nullable();
            $table->decimal('piece_price', 10, 2)->nullable();
            $table->timestamps();

            $table->index('unique_key');
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
