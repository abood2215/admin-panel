<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('document_questions', 'sort')) {
                $table->integer('sort')->unsigned()->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('document_questions', function (Blueprint $table) {
            if (Schema::hasColumn('document_questions', 'sort')) {
                $table->dropColumn('sort');
            }
        });
    }
};