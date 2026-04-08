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
        if (Schema::hasTable('debit_notes') && !Schema::hasColumn('debit_notes', 'debit_note')) {
            Schema::table('debit_notes', function (Blueprint $table) {
                $table->integer('debit_note')->after('bill')->nullable()->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            //
        });
    }
};
