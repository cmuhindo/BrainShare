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
        Schema::create('subtopic_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('generated_course_id');
            $table->string('subtopic_title')->nullable();
            $table->text('content')->nullable();
            $table->json('json_content')->nullable();
            $table->timestamps();

            $table->foreign('generated_course_id')->references('id')->on('generated_courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtopic_contents');
    }
};
