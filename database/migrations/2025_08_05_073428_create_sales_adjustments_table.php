<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('sales_entries', function (Blueprint $table) {
        $table->id();
        $table->string('customer_code')->nullable();
        $table->string('supplier_code')->nullable();
        $table->string('code')->unique(); // GRN or transaction code
        $table->string('item_code');
        $table->string('item_name');
        $table->decimal('weight', 10, 2)->default(0);
        $table->decimal('price_per_kg', 10, 2)->default(0);
        $table->decimal('total', 12, 2)->default(0);
        $table->integer('packs')->default(0);
        $table->string('bill_no')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_adjustments');
    }
};
