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
        Schema::table('receipts', function (Blueprint $table) {
            $table
                ->unsignedBigInteger('order_id')
                ->after('endpoint_id');
            $table
                ->unsignedSmallInteger('confirmation_code')
                ->after('endpoint_id');
            $table
                ->json('result_response')
                ->after('printed')
                ->nullable();
            $table
                ->string('result_message', 96)
                ->after('printed')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('order_id');
            $table->dropColumn('confirmation_code');
            $table->dropColumn('result_response');
            $table->dropColumn('result_message');
        });
    }
};
