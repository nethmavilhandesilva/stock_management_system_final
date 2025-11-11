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
        Schema::table('supplier2s', function (Blueprint $table) {
            // Add all new payment-related fields
            $table->string('payment_method')->nullable()->after('description');
            
            // Cheque fields
           
            $table->string('account_no')->nullable()->after('bank_name');
            $table->string('bank_slip_path')->nullable()->after('account_no');
        });
    }

    public function down()
    {
        Schema::table('supplier2s', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                
                'account_no',
                'bank_slip_path'
            ]);
        });
    }
};
