<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_registered_user_redirects_from_login_page()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/login');

        $this->assertAuthenticated();
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/home');
    }
}
