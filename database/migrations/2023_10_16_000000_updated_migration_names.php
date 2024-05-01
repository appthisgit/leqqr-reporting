<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::update('UPDATE migrations SET migration = "2023_10_16_110813_update_endpoints_name" WHERE migration = "2023_10_16_110813_lengthen_endpoint_name"');
        DB::update('UPDATE migrations SET migration = "2023_10_16_111142_update_templates_name" WHERE migration = "2023_10_16_111142_lengthen_template_name"');
        DB::update('UPDATE migrations SET migration = "2024_03_27_130258_add_results_to_receipts" WHERE migration = "2024_03_27_130258_add_results_to_receipts_table"');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
