<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    public function testHomePage()
    {
        $this->seed();

        // Test without user logged in
        $response = $this->get('/');
        $response->assertStatus(302);

        // Test with user logged in
        $user = User::find(1);
        $this->assertNotNull($user);
        $response = $this->actingAs($user)
            ->get('/');
        $response->assertStatus(200);
    }

    public function testLoginShowsLoginForm()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function testLoginDisplaysValidationErrors()
    {
        $response = $this->post('/login', []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    public function testLoginAuthenticatesAndRedirectsUser()
    {
        //$user = factory(User::class)->create();

        $this->seed();
        $user = User::find(1);

        $response = $this->post(route('login'),
                                [
                                    'email' => $user->email,
                                    'password' => '$Tester1'
                                ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function testRegisterCreatesAndAuthenticatesUser()
    {
        $name = $this->faker->name;
        $email = $this->faker->safeEmail;
        $password = $this->faker->password(8);

        $response = $this->post('register',
                                [
                                    'name' => $name,
                                    'email' => $email,
                                    'password' => $password,
                                    'password_confirmation' => $password
                                ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email
        ]);

        $user = User::where('email', $email)->where('name', $name)->first();
        $this->assertNotNull($user);

        $this->assertAuthenticatedAs($user);
    }

    public function testHomeRequiresAuthentication()
    {
        // Test unauthenticated user
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertLocation('/login');

        // Test authenticated user
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }
}
