<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KeywordTest extends TestCase
{
    use RefreshDatabase;
    
    public function testGetAllKeywords()
    {
        logger("KeywordTest::testGetAllKeywords - Enter");

        // Test with no keywords
        $response = $this->get('/api/keywords');

        $response->assertStatus(200)
            ->assertExactJson([
                'msg' => 'ok',
                'keywords' => [
                ]
            ]);

        // Seed database and test contains expected data.
        $this->seed();
        $this->assertDatabaseCount('keywords', 2);
        $this->assertDatabaseHas('keywords', [
            'name' => 'vancouver',
        ]);

        $this->assertDatabaseHas('keywords', [
            'name' => 'nature'
        ]);

        // Test unauthorized user. Should return all keywords.
        $response = $this->get('/api/keywords');

        $response->assertStatus(200)
            ->assertExactJson([
                'msg' => 'ok',
                'keywords' => [
                    [
                        "id" => "2",
                        "name" => "nature"
                    ],
                    [
                        "id" => "1",
                        "name" => "vancouver"
                    ]
                ]
            ]);

        // Test authorized user.
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/api/keywords');

        $response->assertStatus(200)
            ->assertExactJson([
                'msg' => 'ok',
                'keywords' => [
                    [
                        "id" => "2",
                        "name" => "nature"
                    ],
                    [
                        "id" => "1",
                        "name" => "vancouver"
                    ]
                ]
            ]);

        logger("KeywordTest::testGetAllKeywords - Leave");
    }

    public function testAddPhotoKeyword()
    {
        logger("KeywordTest::testAddPhotoKeyword - Enter");

        $this->seed();

        // Test unauthorized
        $response = $this->json('POST', '/api/keywords/photo/1', ['keyword]' => 'test']);

        $response->assertStatus(401)
                 ->assertExactJson(
                     [
                        'msg' => 'not authorized',
                         'keyword_id' => 0
                     ]
                 );

        // Test authorized user with no keyword supplied
        $user = User::find(1);

        $response = $this->actingAs($user)->json('POST', '/api/keywords/photo/1', ['keyword' => '']);

        $response->assertStatus(400)
            ->assertExactJson(
                [
                    'msg' => 'keyword required',
                    'keyword_id' => 0
                ]
            );

        $response = $this->get('/api/keywords');

        $response->assertStatus(200)
            ->assertExactJson([
                'msg' => 'ok',
                'keywords' => [
                    [
                        "id" => "2",
                        "name" => "nature"
                    ],
                    [
                        "id" => "1",
                        "name" => "vancouver"
                    ]
                ]
            ]);

        // Test authorized user with valid keyword
        $response = $this->actingAs($user)->json('POST', '/api/keywords/photo/1', ['keyword' => 'test']);

        $response->assertStatus(201)
            ->assertExactJson(
                [
                    'msg' => 'ok',
                    'keyword_id' => 3
                ]
            );

        $response = $this->get('/api/keywords');

        $response->assertStatus(200)
            ->assertExactJson([
                'msg' => 'ok',
                'keywords' => [
                    [
                        "id" => "2",
                        "name" => "nature"
                    ],
                    [
                        "id" => "3",
                        "name" => "test"
                    ],
                    [
                        "id" => "1",
                        "name" => "vancouver"
                    ]
                ]
            ]);


        logger("KeywordTest::testAddPhotoKeyword - Leave");
    }
    
    public function testDeleteKeywordFromPhoto()
    {
        logger("testGetAllKeywords::testDeletePhotoKeyword - ENTER");

        $this->seed();

        // Must be an authenticated user
        $response = $this->delete('/api/keywords/1/photo/1');

        $response->assertStatus(401)
            ->assertExactJson([
                'msg' => 'not authorized'
            ]);

        $user = User::find(1);

        // Must also own the photo
        $response = $this->actingAs($user)->delete('/api/keywords/1/photo/3');

        $response->assertStatus(401)
            ->assertExactJson([
                'msg' => 'not authorized'
            ]);

        // Test 404 returned if keyword does not exist for photo
        $response = $this->actingAs($user)->delete('/api/keywords/4/photo/1');

        $response->assertStatus(404)
            ->assertExactJson([
                'msg' => 'keyword or photo not found'
            ]);

        // Test owner can remove existing keyword
        $response = $this->actingAs($user)->delete('/api/keywords/1/photo/1');

        $response->assertStatus(200)
            ->assertExactJson([
                'msg' => 'ok'
            ]);

        logger("KeywordTest::testDeletePhotoKeyword - LEAVE");
    }
}
