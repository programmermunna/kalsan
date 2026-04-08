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
        Schema::table('form_field_responses', function (Blueprint $table) {
            if (!Schema::hasColumn('form_field_responses', 'phone_id')) {
                $table->integer('phone_id')->after('email_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_field_responses', function (Blueprint $table) {
            if (Schema::hasColumn('form_field_responses', 'phone_id')) {
                $table->dropColumn('phone_id');
            }
        });
    }
};
