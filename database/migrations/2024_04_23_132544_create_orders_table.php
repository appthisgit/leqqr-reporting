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
        // Create order table
        Schema::create('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('id')
                ->primary();
            $table->unsignedBigInteger('company_id');
            $table->unsignedSmallInteger('confirmation_code');
            $table->json('data');
        });

        // Move all data to this table
        DB::statement("INSERT INTO orders (`id`, `company_id`, `confirmation_code`, `data`) 
            SELECT `order_id`, `company_id`, `confirmation_code`, `order` 
            FROM receipts 
            WHERE `id` IN (SELECT MAX(`id`) FROM receipts GROUP BY `order_id`)");

        // Update receipts table
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropForeign('receipts_company_id_foreign');
            $table->dropColumn('company_id');
            $table->dropColumn('confirmation_code');
            $table->dropColumn('order');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders');
        });

        // Set relation to orders table after insertion
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('company_id')
                ->references('id')
                ->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
