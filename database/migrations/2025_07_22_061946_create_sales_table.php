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
    Schema::create('sales', function (Blueprint $table) {
        $table->id();
        $table->string('supplier_code');
        $table->string('code');
        $table->string('item_code');
        $table->string('item_name');
        $table->float('weight');
        $table->float('price_per_kg');
        $table->float('total');
        $table->integer('packs');
        $table->date('txn_date');
        $table->string('grn_no');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
