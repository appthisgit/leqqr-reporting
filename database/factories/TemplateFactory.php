<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Template>
 */
class TemplateFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model|TModel>
     */
    protected $model = \App\Models\Receipt\Template::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'xml' => '
<template
    font-size="12"
    receipt-width-char-amount="46"
>

    <image file="banner.png" />
    <line margin-bottom="10" center="true"><company_name /></line>

    <line font-size="60" bold="true" font="SansSerif" center="true" margin-bottom="20"><order_number/></line>

    <line><text>Bestelbon (#</text><order_id/><text>)</text></line>
    <line><order_date/></line>

    <!-- TODO: BRING QR FIELD! -->
    <if key="is_pickup">
        <line bold="true" margin-top="10"><text>Meenemen</text></line>
    </if >
    <!--if key="is_delivery">
        <line><text>Bezorgmoment:</text></line>
        <line><order_ready_date/></line>
    </if-->
    <!--if key="is_pickup">
        <line><text>Afhaalmoment:</text></line>
        <line><order_ready_date/></line>
    </if-->
    <if key="has_buzzer">
        <line margin-top="10"><text>Buzzernummer:</text></line>
        <line><buzzernumber/></line>
    </if>
    <if key="has_tablenumer">
        <line margin-top="10"><text>Tafelnummer:</text></line>
        <line><tablenumber/></line>
    </if>

    <if key="has_notes">
        <line margin-top="10"><text>Notitie:</text></line>
        <line wordwrap="true"><order_notes /></line>
    </if>

    <stripe margin-top="10" />
    <foreach items="products">
        <row>
            <item><product_amount/><text>x </text><product_name /></item>
            <price value="product_subtotal"/>
            <!--<price value="product_price"/>-->
        </row>
        <!-- <if key="has_product_tax">
            <row>
                <item><text> btw </text><product_tax_tarif/><text> %</text></item>
                <price value="product_tax"/>
            </row>
        </if> -->
        <foreach items="variations">
            <row>
                <item><text> - </text><variation_name/></item>
                <!-- <item><variation_symbol/><variation_name/></item>
                <if key="has_variation_price">
                    <price value="variation_price"/>
                </if> -->
            </row>
        </foreach>
        <if key="has_product_notes">
            <line><product_notes wordwrap="true"/></line>
        </if>
    </foreach>
    <if key="subtotal_is_different_than_total">
        <stripe />
        <row margin-bottom="10">
            <item><text>Subtotaal</text></item>
            <price value="subtotal"/>
        </row>
    </if>
    <if key="has_transaction_costs">
        <row>
            <item><text>Transactiekosten</text></item>
            <price value="transaction_costs" />
        </row>
    </if>
    <if key="has_discounts">
        <row margin-top="10">
            <item><text>Korting</text></item>
            <price value="discount_amount" />
        </row>
    </if>

    <stripe />
    <row>
        <item><text>Totaal</text></item>
        <price value="total" />
    </row>

    <if key="has_taxes">
        <line margin-top="10"><text>BTW</text></line>
        <foreach items="taxes">
            <row>
                <item><tax_tarif/><text>%</text></item>
                <price value="tax" />
            </row>
        </foreach>
    </if>

    <line margin-top="10"><text>Betaalmethode:</text></line>
    <if key="is_method_cash">
        <line><text>contant</text></line>
    </if>
    <if key="is_method_account">
        <line><text>op rekening</text></line>
    </if>
    <if key="is_method_online">
        <line><text>online betaald</text></line>
    </if>
    <if key="is_method_pin">
        <line><text>pinautomaat</text></line>
    </if>
    <if key="is_method_cikam">
        <line><text>cikam</text></line>
    </if>
    <if key="is_method_other">
        <line><payment_method /></line>
    </if>

    <!-- <image margin-top="8" file="footer.png" /> -->

    <line margin-top="20" font-size="10" font="Monospaced"><pin_receipt /></line>

</template>
        '];
    }
}
