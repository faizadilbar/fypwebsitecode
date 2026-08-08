<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('quiz_code', 10)->unique();
            $table->string('quiz_name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('course_id');
            $table->date('quiz_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration')->nullable();
            $table->integer('total_questions')->default(0);
            $table->decimal('total_marks', 8, 2)->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->boolean('is_poll')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
