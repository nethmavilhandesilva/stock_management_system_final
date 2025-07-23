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
        Schema::table('sales', function (Blueprint $table) {
            // Check if 'bill_printed' column exists. If so, rename it.
            if (Schema::hasColumn('sales', 'bill_printed')) {
                $table->renameColumn('bill_printed', 'Processed');
            } else {
                // If 'bill_printed' doesn't exist, add 'Processed' directly.
                // It should be a string of length 1, defaulting to 'N'.
                // Adjust 'after()' to place it logically in your table, e.g., after 'packs' or 'total'.
                $table->string('Processed', 1)->default('N')->after('packs');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            //
        });
    }
};
