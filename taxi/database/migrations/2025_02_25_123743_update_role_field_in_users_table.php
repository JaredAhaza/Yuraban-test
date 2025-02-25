<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, change the enum type to include admin
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'driver', 'admin') NOT NULL DEFAULT 'customer'");
        
        // Alternative approach if you're using a different database or if above doesn't work
        // This changes the column to a string which can accept any value
        /*
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->change();
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If you need to revert, you can change it back to the original enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'driver') NOT NULL DEFAULT 'customer'");
    }
};