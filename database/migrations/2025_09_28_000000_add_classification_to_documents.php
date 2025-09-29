<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents','stream_id')) {
                $table->foreignId('stream_id')->nullable()->constrained('streams')->nullOnDelete();
            }
            if (!Schema::hasColumn('documents','year_id')) {
                $table->foreignId('year_id')->nullable()->constrained('years')->nullOnDelete();
            }
            if (!Schema::hasColumn('documents','subject_id')) {
                $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents','subject_id')) {
                $table->dropConstrainedForeignId('subject_id');
            }
            if (Schema::hasColumn('documents','year_id')) {
                $table->dropConstrainedForeignId('year_id');
            }
            if (Schema::hasColumn('documents','stream_id')) {
                $table->dropConstrainedForeignId('stream_id');
            }
        });
    }
};
