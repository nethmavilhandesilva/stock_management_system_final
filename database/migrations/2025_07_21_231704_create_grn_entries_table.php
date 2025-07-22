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
    Schema::create('grn_entries', function (Blueprint $table) {
        $table->id();
        $table->string('auto_purchase_no')->unique();
        $table->string('code');
        $table->string('supplier_code');
        $table->string('item_code');
        $table->string('item_name');
        $table->integer('packs');
        $table->float('weight');
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
        Schema::dropIfExists('grn_entries');
    }
};
