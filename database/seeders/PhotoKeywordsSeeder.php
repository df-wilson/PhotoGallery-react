<?php namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhotoKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('photo_keywords')->insert([
            'photo_id' => 1,
            'keyword_id' => 1
        ]);

        DB::table('photo_keywords')->insert([
            'photo_id' => 2,
            'keyword_id' => 1
        ]);

        DB::table('photo_keywords')->insert([
            'photo_id' => 3,
            'keyword_id' => 2
        ]);

        DB::table('photo_keywords')->insert([
            'photo_id' => 4,
            'keyword_id' => 2
        ]);
    }
}
