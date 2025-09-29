<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'semester')) {
                $table->enum('semester', ['first','second'])->nullable()->after('year_id');
            }
            if (!Schema::hasColumn('documents', 'specialty_id')) {
                $table->unsignedBigInteger('specialty_id')->nullable()->after('stream_id');
                $table->foreign('specialty_id')->references('id')->on('specialties')->nullOnDelete();
            }
        });
    }

    public function down(): void {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'semester')) $table->dropColumn('semester');
            if (Schema::hasColumn('documents', 'specialty_id')) {
                $table->dropForeign(['specialty_id']);
                $table->dropColumn('specialty_id');
            }
        });
    }
};
