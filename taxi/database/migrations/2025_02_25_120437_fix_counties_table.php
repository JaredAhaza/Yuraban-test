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
        // Create counties table only if it doesn't exist
        if (!Schema::hasTable('counties')) {
            Schema::create('counties', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->json('sub_counties')->nullable();
                $table->timestamps();
            });
        } else {
            // Table exists, ensure required columns exist
            Schema::table('counties', function (Blueprint $table) {
                if (!Schema::hasColumn('counties', 'sub_counties')) {
                    $table->json('sub_counties')->nullable();
                }
                
                // Add other missing columns if needed
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to drop anything in the down method
    }
};