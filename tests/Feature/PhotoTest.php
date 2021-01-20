<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIsUserPhotoOwner()
    {
        $this->seed();

        $isOwner = Photo::isUserPhotoOwner(1,1);
        $this->assertTrue($isOwner);

        $isOwner = Photo::isUserPhotoOwner(1,3);
        $this->assertTrue(!$isOwner);
    }

    public function testGetNextPhoto()
    {
        logger("PhotoTest::testGetNextPhoto - Enter");

        $response = $this->get('/api/photos/1/next');

        // Test for unauthorized user
        $response->assertStatus(401)
                 ->assertExactJson([
                   ]);

        // Test for authorized user.
        $this->seed();
        $user = User::find(1);
        $response = $this->actingAs($user)->get('/api/photos/1/next');

        $response->assertStatus(200)
                 ->assertExactJson([
                     "description" => "Railroad tracks looking south towards Kerrisdale in Vancouver. The track have been removed since this picture was taken and replaced by a bike/pedestrian path.",
                     "filepath" => "/storage/images/RailroadToKerrisdale.jpg",
                     "id" => "2",
                     "is_public" =>"1",
                     "name" =>"Kerrisdale Tracks"
                   ]);

        // Test for already at last photo
        $response = $this->actingAs($user)->get('/api/photos/3/next');

        $response->assertStatus(200)
            ->assertExactJson([
            ]);

        logger("PhotoTest::testGetNextPhoto - Leave");
    }

    public function testGetPrevPhoto()
    {
        logger("PhotoTest::testGetPrevPhoto - Enter");

        $response = $this->get('/api/photos/1/prev');

        // Test for unauthorized user
        $response->assertStatus(401)
            ->assertExactJson([
            ]);

        // Test for authorized user.
        $this->seed();
        $user = User::find(1);
        $response = $this->actingAs($user)->get('/api/photos/2/prev');

        $response->assertStatus(200)
            ->assertExactJson([
                "description" => "Vancouver near Stanley Park",
                "filepath" => "/storage/images/2012_04_14_070.jpg",
                "id" => "1",
                "is_public" =>"1",
                "name" =>"Vancouver"
            ]);

        // Test for already at first photo
        $response = $this->actingAs($user)->get('/api/photos/1/prev');

        $response->assertStatus(200)
            ->assertExactJson([
            ]);

        logger("PhotoTest::testGetNextPhoto - Leave");
    }
}
