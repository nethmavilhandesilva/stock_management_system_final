<?php

// database/migrations/YYYY_MM_DD_HHMMSS_add_bill_printed_and_processed_to_sales_table.php

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
        Schema::table('sales', function (Blueprint $table) {
            // Check and add 'bill_printed' column if it doesn't exist
            if (!Schema::hasColumn('sales', 'bill_printed')) {
                $table->string('bill_printed', 1)->default('N')->after('packs'); // Adjust 'after' as per your existing schema
            }
            
            // Check and add 'Processed' column if it doesn't exist
            if (!Schema::hasColumn('sales', 'Processed')) {
                $table->string('Processed', 1)->default('N')->after('bill_printed'); // Place it after bill_printed
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Drop 'bill_printed' column on rollback if it was added by this migration
            if (Schema::hasColumn('sales', 'bill_printed') && env('APP_ENV') === 'local') { // Optional: only drop in local env
                 $table->dropColumn('bill_printed');
            }
            // Drop 'Processed' column on rollback
            if (Schema::hasColumn('sales', 'Processed')) {
                $table->dropColumn('Processed');
            }
        });
    }
};