<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriginDestinationToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 이미 컬럼이 존재할 수 있으므로, 존재 여부를 확인하고 추가
        if (!Schema::hasColumn('invoices', 'origin')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('origin')->nullable()->after('p_email');
            });
        }

        if (!Schema::hasColumn('invoices', 'destination')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('destination')->nullable()->after('origin');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('invoices', 'destination')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('destination');
            });
        }

        if (Schema::hasColumn('invoices', 'origin')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('origin');
            });
        }
    }
}


