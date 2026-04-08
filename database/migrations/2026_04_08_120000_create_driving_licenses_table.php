<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('driving_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->string('license_number')->unique();
            $table->string('grade')->default('A1');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('serial_number')->unique();
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // active, expired, suspended
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index(['license_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('driving_licenses');
    }
};
