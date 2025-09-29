<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->integer('question_index');
            $table->text('answer')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'document_id', 'question_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_answers');
    }
};
