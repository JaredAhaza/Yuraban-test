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
        Schema::table('rides', function (Blueprint $table) {
            $table->json('declined_by')->nullable(); // Store multiple driver IDs as JSON
        });
    }
    
    public function down()
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn('declined_by');
        });
    }
    
};
