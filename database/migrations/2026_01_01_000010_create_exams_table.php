<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('course_code')->nullable()->index();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('duration_minutes')->default(45);
            $table->decimal('pass_percentage', 5, 2)->default(70.00);
            $table->integer('max_strikes')->default(3);
            $table->string('access_passcode')->nullable();
            $table->boolean('randomize_questions')->default(true);
            $table->boolean('randomize_options')->default(true);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_released')->default(false); // Live waiting room toggle
            $table->timestamp('released_at')->nullable();
            $table->string('anti_cheat_strictness')->default('strict'); // strict, medium, lenient
            $table->boolean('enforce_fullscreen')->default(true);
            $table->boolean('block_clipboard')->default(true);
            $table->boolean('block_devtools')->default(true);
            $table->boolean('watermark_enabled')->default(true);
            $table->string('watermark_custom_text')->nullable();
            $table->timestamp('scheduled_start')->nullable();
            $table->timestamp('scheduled_end')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
