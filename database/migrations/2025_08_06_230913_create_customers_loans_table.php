<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('loan_type'); // "old" or "today"
            $table->string('settling_way'); // "cash" or "cheque"
            $table->string('bill_no')->nullable();
            $table->string('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('cheque_no')->nullable();
            $table->string('bank')->nullable();
            $table->date('cheque_date')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers_loans');
    }
};
