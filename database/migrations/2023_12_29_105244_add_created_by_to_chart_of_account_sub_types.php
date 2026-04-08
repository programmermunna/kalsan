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
        if (!Schema::hasColumn('chart_of_account_sub_types', 'created_by')) {

            Schema::table('chart_of_account_sub_types', function (Blueprint $table) {
                $table->integer('created_by')->after('type');                        
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_account_sub_types', function (Blueprint $table) {
            //
        });
    }
};
