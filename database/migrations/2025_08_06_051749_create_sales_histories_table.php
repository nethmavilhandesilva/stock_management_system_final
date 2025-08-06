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
        Schema::create('sales_histories', function (Blueprint $table) {
            $table->id();

            $table->string('customer_name')->nullable();
            $table->string('customer_code');
            $table->string('supplier_code');
            $table->string('code'); // GRN Code
            $table->string('item_code');
            $table->string('item_name');
            $table->decimal('weight', 10, 2);
            $table->decimal('price_per_kg', 10, 2);
            $table->decimal('total', 12, 2);
            $table->integer('packs');
            $table->boolean('bill_printed')->default(false);
            $table->string('Processed')->default('N');
            $table->string('bill_no')->nullable();
            $table->boolean('updated')->default(false);
            $table->boolean('is_printed')->default(false);
            $table->timestamp('CustomerBillEnteredOn')->nullable();
            $table->timestamp('FirstTimeBillPrintedOn')->nullable();
            $table->timestamp('BillChangedOn')->nullable();
            $table->string('UniqueCode')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_histories');
    }
};
