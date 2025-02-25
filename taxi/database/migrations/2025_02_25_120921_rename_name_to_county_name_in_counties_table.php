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
        Schema::table('counties', function (Blueprint $table) {
            // First check if the old column exists and new one doesn't
            if (Schema::hasColumn('counties', 'name') && !Schema::hasColumn('counties', 'county_name')) {
                // Rename the column
                $table->renameColumn('name', 'county_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('counties', function (Blueprint $table) {
            // Check if the new column exists and old one doesn't
            if (Schema::hasColumn('counties', 'county_name') && !Schema::hasColumn('counties', 'name')) {
                // Rename back
                $table->renameColumn('county_name', 'name');
            }
        });
    }
};