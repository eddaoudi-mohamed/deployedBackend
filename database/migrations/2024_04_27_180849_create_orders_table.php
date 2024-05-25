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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on("clients");
            $table->decimal("amount");
            $table->decimal("paid");
            $table->enum("status", ["pending", "paid", "unpaid", "partially_paid", "refunded", "partially_refunded", "voided", "deleted"])->default("pending");
            $table->timestamps();
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


// to now more about status of order visit this link
// https://help.shopify.com/en/manual/orders/manage-orders/order-status