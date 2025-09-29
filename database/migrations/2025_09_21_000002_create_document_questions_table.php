<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->text('question');
            $table->json('options')->nullable();
            $table->string('correct_answer')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();

            // 👇 مهم جداً مع العربية/الرموز
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_questions');
    }
};
