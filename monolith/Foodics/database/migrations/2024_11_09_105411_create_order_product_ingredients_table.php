<?php

use App\Enums\ProductIngredientStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_product_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_product_id')->constrained('order_product')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained('ingredient_product')->cascadeOnDelete();
            $table->integer(column: 'quantity', unsigned: true);
            $table->enum('status', array_column(ProductIngredientStatus::cases(), 'value'))->default(ProductIngredientStatus::Reserved);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product_ingredients');
    }
};
