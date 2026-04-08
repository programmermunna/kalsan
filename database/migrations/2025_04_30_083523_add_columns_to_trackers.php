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
        Schema::table('time_trackers', function (Blueprint $table) {
            if (!Schema::hasColumn('time_trackers', 'user_id')) {
                $table->integer('user_id')->after('is_active');
            }
        });

        Schema::table('track_photos', function (Blueprint $table) {
            if (!Schema::hasColumn('track_photos', 'created_by')) {
                $table->integer('created_by')->after('status');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_trackers', function (Blueprint $table) {
            if (Schema::hasColumn('time_trackers', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });

        Schema::table('track_photos', function (Blueprint $table) {
            if (Schema::hasColumn('track_photos', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
};
