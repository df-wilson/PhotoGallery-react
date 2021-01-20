<?php namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('keywords')->insert([
            'name' => 'vancouver'
        ]);
        DB::table('keywords')->insert([
            'name' => 'nature'
        ]);
    }
}
