<?php namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('photos')->insert([
            'user_id'            => 1,
            'name'               => "Vancouver",
            'filepath'           => "/storage/images/2012_04_14_070.jpg",
            'thumbnail_filepath' => "/storage/images/thumb_2012_04_14_070.jpg",
            'is_public'          => true,
            'description'        => "Vancouver near Stanley Park",
            'created_at'         => '2018-05-16 01:32:33',
            'updated_at'         => '2018-05-18 03:46:33'
        ]);

        DB::table('photos')->insert([
            'user_id'     => 1,
            'name'        => "Kerrisdale Tracks",
            'filepath'    => "/storage/images/RailroadToKerrisdale.jpg",
            'thumbnail_filepath' => "/storage/images/thumb_RailroadToKerrisdale.jpg",
            'is_public'   => true,
            'description' => "Railroad tracks looking south towards Kerrisdale in Vancouver. The track have been removed since this picture was taken and replaced by a bike/pedestrian path.",
            'created_at'  => '2020-05-29 21:24:04',
            'updated_at'  => '2020-05-29 21:34:11'
        ]);

        DB::table('photos')->insert([
            'user_id'     => 2,
            'name'        => "Giraffe",
            'filepath'    => "/storage/images/2012_04_09_012.jpg",
            'thumbnail_filepath' => "/storage/images/thumb_2012_04_09_012.jpg",
            'is_public'   => false,
            'description' => "A picture of a giraffe.",
            'created_at'  => '2018-05-16 02:53:40',
            'updated_at'  => '2018-05-16 03:14:11'
        ]);

        DB::table('photos')->insert([
            'user_id'     => 2,
            'name'        => "Flamingo",
            'filepath'    => "/storage/images/2012_04_09_016.jpg",
            'thumbnail_filepath' => "/storage/images/thumb_2012_04_09_016.jpg",
            'is_public'   => true,
            'description' => "A picture of a flamingo.",
            'created_at'  => '2019-10-01 12:20:02',
            'updated_at'  => '2019-10-01 12:20:02'
        ]);
    }
}
