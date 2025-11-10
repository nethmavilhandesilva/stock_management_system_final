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
    Schema::table('grn_entries', function (Blueprint $table) {
        $table->boolean('is_read')->default(0)->after('BP'); // Place after an existing column
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grn_entries', function (Blueprint $table) {
            //
        });
    }
};
