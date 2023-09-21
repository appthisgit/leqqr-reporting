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
        // Schema::create('orders', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });



        // CREATE TABLE `orders` (
        //     `id` int unsigned NOT NULL AUTO_INCREMENT,
        //     `company_id` int unsigned NOT NULL,
        //     `user_id` int NOT NULL,
        //     `cart_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `grouporder_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `confirmation_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `order_ready` datetime NOT NULL,
        //     `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `postal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `price_subtotal` double(8,2) NOT NULL,
        //     `price_shipment` double(8,2) NOT NULL,
        //     `price_transaction` double(8,2) NOT NULL,
        //     `price_discount` double(8,2) NOT NULL,
        //     `price_total` double(8,2) NOT NULL,
        //     `coupons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
        //     `products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
        //     `shipment_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `mollie_payment_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `mollie_payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        //     `is_seen` int NOT NULL,
        //     `is_printed` int NOT NULL,
        //     `is_test_order` int NOT NULL,
        //     `grouporder_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
        //     `meta_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
        //     `created_at` timestamp NULL DEFAULT NULL,
        //     `updated_at` timestamp NULL DEFAULT NULL,
        //     `as_soon_as_possible` tinyint(1) NOT NULL DEFAULT '0',
        //     `pin_terminal_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        //     `pin_payment_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        //     `pin_transaction_receipt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        //     `table_nr` int DEFAULT NULL,
        //     `buzzer_nr` int DEFAULT NULL,
        //     `vat` json DEFAULT NULL,
        //     `pay_data` json DEFAULT NULL,
        //     `pin_amount` double(8,2) NOT NULL DEFAULT '0.00',
        //     `cardbrand_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        //     `transaction_fee` double(8,2) NOT NULL DEFAULT '0.00',
        //     `payment_fee` double(10,3) NOT NULL DEFAULT '0.000',
        //     `week_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        //     `clearing_provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        //     `origin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kiosk',
        //     `location_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        //     `customer_ideal_fee` double(8,2) NOT NULL DEFAULT '0.00',
        //     `questions_data` json DEFAULT NULL,
        //     PRIMARY KEY (`id`),
        //     KEY `orders_pin_payment_id_index` (`pin_payment_id`),
        //     KEY `orders_company_id_index` (`company_id`),
        //     KEY `orders_status_index` (`status`),
        //     KEY `orders_status_company_id_index` (`status`,`company_id`),
        //     KEY `orders_cart_id_index` (`cart_id`),
        //     KEY `orders_week_id_index` (`week_id`),
        //     KEY `orders_clearing_provider_index` (`clearing_provider`),
        //     CONSTRAINT `orders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
        //   )
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('orders');
    }
};
