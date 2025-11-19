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
    Schema::create('courses', function (Blueprint $table) {
        $table->id();
        $table->string('course_no');
        $table->string('descriptive_title');
        $table->string('course_components');
        $table->integer('units');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('courses');
}

};
