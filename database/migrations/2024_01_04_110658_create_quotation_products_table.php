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
        Schema::create('quotation_products', function (Blueprint $table) {
            $table->id();
            $table->integer('quotation_id')->default('0');
            $table->integer('product_id')->default('0');
            $table->integer('quantity')->default('0');
            $table->string('tax')->default('0.00')->nullable();
            $table->float('discount')->default('0.00')->nullable();
            $table->decimal('price',15,2)->default('0.00');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_products');
    }
};
