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
        Schema::create('raw_grades', function (Blueprint $table) {
            $table->id('rawID');
            $table->unsignedBigInteger('classID')->nullable();
            $table->string('course_no')->nullable();
            $table->string('descriptive_title')->nullable();
            $table->string('instructor')->nullable();
            $table->string('academic_period')->nullable();
            $table->string('schedule')->nullable();
            $table->unsignedBigInteger('studentID')->nullable();
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->string('email')->nullable();
            $table->string('program')->nullable();
            $table->string('department')->nullable();
            $table->decimal('prelim', 5, 2)->nullable();
            $table->decimal('midterm_raw', 5, 2)->nullable();
            $table->decimal('midterm', 5, 2)->nullable();
            $table->decimal('semi_finals_raw', 5, 2)->nullable();
            $table->decimal('semi_finals', 5, 2)->nullable();
            $table->decimal('final_raw', 5, 2)->nullable();
            $table->decimal('final', 5, 2)->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps(); // includes created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_grades');
    }
};
