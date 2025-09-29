؟php<?php
// database/seeders/CategorySeeder.php
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder {
    public function run(): void
{
    // جذور
    $exam = \App\Models\Category::create([
        'slug'=>'exams', 'name_ar'=>'امتحانات', 'name_en'=>'Exams'
    ]);
    $topic = \App\Models\Category::create([
        'slug'=>'topics', 'name_ar'=>'محاور', 'name_en'=>'Topics'
    ]);

    // أطفال
    \App\Models\Category::insert([
        ['slug'=>'final','name_ar'=>'نهائي','name_en'=>'Final','parent_id'=>$exam->id],
        ['slug'=>'midterm','name_ar'=>'نصفي','name_en'=>'Midterm','parent_id'=>$exam->id],
        ['slug'=>'algebra','name_ar'=>'جبر','name_en'=>'Algebra','parent_id'=>$topic->id],
        ['slug'=>'geometry','name_ar'=>'هندسة','name_en'=>'Geometry','parent_id'=>$topic->id],
    ]);
}

}
