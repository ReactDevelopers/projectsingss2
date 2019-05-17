<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_registered_user_redirects_from_register_page()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/register');

        $this->assertAuthenticated();
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/home');
    }

    public function test_valid_user_registration()
    {
        Notification::fake();
        Notification::assertNothingSent();

        $password = $this->faker->password;

        $response = $this->json('POST', '/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $password,
            'password_confirmation' => $password,
            'terms' => true,
            'plan' => 'free',
        ]);

        $user = User::first();

        // Notification::assertSentTo(
        //     [$user], \Illuminate\Auth\Notifications\VerifyEmail::class
        // );

        $response->assertSessionHasNoErrors()
                ->assertStatus(200)
                ->assertJson([
                    'redirect' => '/home'
                ]);
    }
}
