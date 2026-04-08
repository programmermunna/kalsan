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
        if (!Schema::hasTable('customer_debit_notes')) {
            Schema::create('customer_debit_notes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('debit_id')->nullable()->default(0);
                $table->integer('bill')->default('0');
                $table->integer('bill_product')->nullable()->default(0);
                $table->decimal('amount', 15, 2)->default('0.00');
                $table->date('date');
                $table->integer('status')->default('0');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_debit_notes');
    }
};
