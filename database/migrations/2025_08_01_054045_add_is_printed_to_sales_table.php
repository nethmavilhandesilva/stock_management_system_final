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
            // Add the new column. It's a boolean with a default of false (0).
            $table->boolean('is_printed')->default(0)->after('updated');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // To reverse the migration, we drop the column.
            $table->dropColumn('is_printed');
        });
    }
};
