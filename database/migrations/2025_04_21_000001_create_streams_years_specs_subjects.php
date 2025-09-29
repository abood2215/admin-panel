<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('streams', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();       // scientific | literary
            $t->string('name_ar');
            $t->string('name_en');
            $t->timestamps();
        });

        Schema::create('years', function (Blueprint $t) {
            $t->id();
            $t->unsignedSmallInteger('year')->unique(); // 2007, 2008, ..
            $t->timestamps();
        });

        Schema::create('specialties', function (Blueprint $t) {
            $t->id();
            $t->foreignId('stream_id')->constrained('streams')->cascadeOnDelete();
            $t->string('name_ar');
            $t->string('name_en');
            $t->timestamps();
        });

        Schema::create('subjects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('specialty_id')->constrained('specialties')->cascadeOnDelete();
            $t->string('name_ar');
            $t->string('name_en');
            $t->timestamps();
        });

        // تعديل جدول documents لربط المفاتيح
        Schema::table('documents', function (Blueprint $t) {
            if (!Schema::hasColumn('documents','stream_id')) {
                $t->foreignId('stream_id')->nullable()->constrained('streams')->nullOnDelete()->after('language');
            }
            if (!Schema::hasColumn('documents','year_id')) {
                $t->foreignId('year_id')->nullable()->constrained('years')->nullOnDelete()->after('stream_id');
            }
            if (!Schema::hasColumn('documents','specialty_id')) {
                $t->foreignId('specialty_id')->nullable()->constrained('specialties')->nullOnDelete()->after('year_id');
            }
            if (!Schema::hasColumn('documents','subject_id')) {
                $t->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete()->after('specialty_id');
            }
            $t->index(['stream_id','year_id','specialty_id','subject_id','status']);
        });
    }

    public function down(): void {
        Schema::table('documents', function (Blueprint $t) {
            foreach (['subject_id','specialty_id','year_id','stream_id'] as $col) {
                if (Schema::hasColumn('documents',$col)) $t->dropConstrainedForeignId($col);
            }
        });
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('specialties');
        Schema::dropIfExists('years');
        Schema::dropIfExists('streams');
    }
};
