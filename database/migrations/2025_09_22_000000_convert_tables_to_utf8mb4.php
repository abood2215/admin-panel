<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // بدّل اسم قاعدة البيانات لو لازم
        // DB::statement("ALTER DATABASE `my_laravel1` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // حوّل جداول مهمة:
        DB::statement("ALTER TABLE `documents` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        DB::statement("ALTER TABLE `document_questions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        DB::statement("ALTER TABLE `document_answers` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        // أضف جداول أخرى إن لزم
    }

    public function down(): void
    {
        // عادةً ما نتركه فارغ؛ الرجوع غير ضروري
    }
};
