<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StreamsYearsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('streams')->upsert([
            ['slug'=>'scientific','name_ar'=>'علمي','name_en'=>'Scientific'],
            ['slug'=>'literary',  'name_ar'=>'أدبي','name_en'=>'Literary'],
        ], ['slug']);

        $years = range(2007, (int)date('Y'));
        DB::table('years')->upsert(array_map(fn($y)=>['year'=>$y], $years), ['year']);

        $scientificId = DB::table('streams')->where('slug','scientific')->value('id');
        $literaryId   = DB::table('streams')->where('slug','literary')->value('id');

        $specSci = DB::table('specialties')->insertGetId([
            'stream_id'=>$scientificId, 'name_ar'=>'علمي عام', 'name_en'=>'General Science'
        ]);
        $specLit = DB::table('specialties')->insertGetId([
            'stream_id'=>$literaryId, 'name_ar'=>'أدبي عام', 'name_en'=>'General Literary'
        ]);

        DB::table('subjects')->upsert([
            ['specialty_id'=>$specSci,'name_ar'=>'رياضيات','name_en'=>'Mathematics'],
            ['specialty_id'=>$specSci,'name_ar'=>'فيزياء','name_en'=>'Physics'],
            ['specialty_id'=>$specLit,'name_ar'=>'تاريخ','name_en'=>'History'],
            ['specialty_id'=>$specLit,'name_ar'=>'جغرافيا','name_en'=>'Geography'],
        ], ['specialty_id','name_en']);
    }
}
