<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicPagesAsGuestTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_welcome_page_renders()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Register');
        $response->assertSee('Sign In');
    }

    public function test_login_page_renders()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('E-Mail');
        $response->assertSee('Password');
        $response->assertSee('Remember Me');
        $response->assertSee('Forgot Your Password');
    }

    public function test_register_page_renders()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Name');
        $response->assertSee('E-Mail Address');
        $response->assertSee('Password');
        $response->assertSee('Confirm Password');
        $response->assertSee('Terms Of Service');
    }

    public function test_forgot_password_page_renders()
    {
        $response = $this->get('/password/reset');

        $response->assertStatus(200);
        $response->assertSee('E-Mail Address');
    }

    public function test_terms_of_service_page_renders()
    {
        $response = $this->get('/terms');

        $response->assertStatus(200);
        $response->assertSee('Terms of Service');
    }
}
