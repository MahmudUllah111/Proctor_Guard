<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->string('session_token', 64)->unique();
            $table->string('student_id_code')->index();
            $table->string('student_name');
            $table->string('student_email')->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status')->default('waiting_room'); // waiting_room, in_progress, completed, terminated_cheat
            $table->decimal('score', 6, 2)->default(0.00);
            $table->decimal('max_score', 6, 2)->default(0.00);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->boolean('passed')->default(false);
            $table->integer('strikes_count')->default(0);
            $table->integer('max_strikes')->default(3);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->text('termination_reason')->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->json('question_order')->nullable();
            $table->json('answers')->nullable();
            $table->boolean('email_report_dispatched')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
