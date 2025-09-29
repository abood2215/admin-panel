<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // حوِّل نوع العمود إلى نص
        DB::statement("ALTER TABLE `documents` MODIFY `semester` VARCHAR(10) NULL");

        // لو كانت هناك قيَم رقمية قديمة 1/2، حوّلها لنص
        DB::statement("UPDATE `documents` SET `semester`='first'  WHERE `semester`='1'");
        DB::statement("UPDATE `documents` SET `semester`='second' WHERE `semester`='2'");
    }

    public function down(): void
    {
        // رجوع إلى INT (اختياري)
        DB::statement("ALTER TABLE `documents` MODIFY `semester` INT NULL");

        // تحويل عكسي اختياري
        DB::statement("UPDATE `documents` SET `semester`='1' WHERE `semester`='first'");
        DB::statement("UPDATE `documents` SET `semester`='2' WHERE `semester`='second'");
    }
};
