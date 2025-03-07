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
        Schema::create('generated_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key for the user
            $table->unsignedBigInteger('subscription_id')->nullable(); // Foreign key for the subscription, nullable if optional
            $table->string('course_title');
            $table->string('class')->nullable();;
            $table->text('course_description')->nullable();;
            $table->text('course_content')->nullable();;
            $table->json('json_content')->nullable();; // To store the JSON of the generated course content
            $table->timestamps();

            // Adding foreign key constraints (optional but recommended)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //$table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_courses');
    }
};
