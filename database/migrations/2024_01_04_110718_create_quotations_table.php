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
        Schema::create('quotations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quotation_id')->default('0');
            $table->unsignedBigInteger('customer_id')->default('0');
            $table->integer('warehouse_id')->default('0');
            $table->date('quotation_date')->nullable();
            $table->integer('category_id')->default('0');
            $table->integer('status')->default('0');
            $table->integer('converted_pos_id')->default('0');
            $table->integer('is_converted')->default('0');
            $table->integer('created_by')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
