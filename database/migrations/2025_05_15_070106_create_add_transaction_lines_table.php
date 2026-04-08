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
        if (!Schema::hasTable('add_transaction_lines')) {
            Schema::create('add_transaction_lines', function (Blueprint $table) {
                $table->id();
                $table->integer('account_id')->nullable();
                $table->string('reference')->nullable();
                $table->integer('reference_id')->default('0');
                $table->integer('reference_sub_id')->default('0');
                $table->date('date');
                $table->double('credit', 15, 2)->default('0.00');
                $table->double('debit', 15, 2)->default('0.00');
                $table->integer('created_by')->default('0');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_transaction_lines');
    }
};
