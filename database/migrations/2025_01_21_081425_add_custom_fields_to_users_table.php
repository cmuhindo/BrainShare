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
        Schema::table('users', function (Blueprint $table) {
            //            
                $table->string('first_name')->nullable(false);
                $table->string('last_name')->nullable(false);
                $table->string('academic_level')->nullable(false);
                $table->string('gender')->nullable(false);
                $table->date('date_of_birth')->nullable(); // Allow null values
                $table->string('country')->nullable(false);
                $table->string('username')->unique()->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
