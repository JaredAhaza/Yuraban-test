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
            // Check if the column doesn't exist before adding it
            if (!Schema::hasColumn('counties', 'name')) {
                $table->string('name');
            }
            
            // Alternatively, rename it to county_name if that's what you want
            if (Schema::hasColumn('counties', 'name') && !Schema::hasColumn('counties', 'county_name')) {
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
            // Choose one of these options depending on what you did in up()
            
            // If you just checked for existence:
            if (Schema::hasColumn('counties', 'name')) {
                $table->dropColumn('name');
            }
            
            // OR if you renamed to county_name:
            // if (Schema::hasColumn('counties', 'county_name')) {
            //     $table->renameColumn('county_name', 'name');
            // }
        });
    }
};