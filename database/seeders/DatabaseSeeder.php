<?php namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(KeywordSeeder::class);
        $this->call(PhotoSeeder::class);
        $this->call(PhotoKeywordsSeeder::class);
    }
}
