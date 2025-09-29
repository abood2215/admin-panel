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
    Schema::create('category_document', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->foreignId('document_id')->constrained()->cascadeOnDelete();
        $table->unique(['category_id','document_id']);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('category_document');
}

};
