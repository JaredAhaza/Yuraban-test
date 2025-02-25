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
        // First check if columns don't already exist
        if (!Schema::hasColumn('users', 'county_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('county_id')->nullable();
            });
        }
        
        if (!Schema::hasColumn('users', 'subcounty')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('subcounty')->nullable();
            });
        }
        
        // Add foreign key if it doesn't exist
        if (!Schema::hasColumn('users', 'county_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('county_id')->references('id')->on('counties');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'county_id')) {
                $table->dropForeign(['county_id']);
                $table->dropColumn('county_id');
            }
            
            if (Schema::hasColumn('users', 'subcounty')) {
                $table->dropColumn('subcounty');
            }
        });
    }
};