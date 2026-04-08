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
        if (Schema::hasTable('credit_notes') && !Schema::hasColumn('credit_notes', 'credit_note')) {
            Schema::table('credit_notes', function (Blueprint $table) {
                $table->integer('credit_note')->after('invoice')->nullable()->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            //
        });
    }
};
