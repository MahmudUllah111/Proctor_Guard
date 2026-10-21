<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proctoring_infractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->onDelete('cascade');
            $table->string('infraction_type'); // visibility_hidden, window_blur, fullscreen_exit, devtools_opened, devtools_docked, inspect_shortcut_pressed, context_menu_attempt, tamper_attempt
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->integer('strike_number')->default(1);
            $table->text('details')->nullable();
            $table->string('client_timestamp')->nullable();
            $table->timestamp('server_timestamp')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proctoring_infractions');
    }
};
