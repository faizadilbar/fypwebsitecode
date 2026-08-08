<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('student_id');
            $table->string('student_name')->nullable();
            $table->string('student_identifier')->nullable();
            $table->string('quiz_code')->nullable();
            $table->string('course_name')->nullable();
            $table->enum('status', ['started', 'abandoned', 'submitted'])->default('started');
            $table->integer('tab_switch_count')->default(0);
            $table->boolean('allowed_reentry')->default(false);
            $table->timestamp('loaded_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
