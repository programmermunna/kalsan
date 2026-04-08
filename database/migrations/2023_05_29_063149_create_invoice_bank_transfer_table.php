<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_bank_transfers', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id');
            $table->integer('order_id');
            $table->decimal('amount',15,2)->default('0.00');
            $table->string('status')->nullable();
            $table->date('date')->nullable();
            $table->string('receipt')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_bank_transfers');
    }
};
