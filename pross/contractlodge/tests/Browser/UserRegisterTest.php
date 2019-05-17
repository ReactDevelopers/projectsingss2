<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;

class UserRegisterTest extends DuskTestCase
{
    use WithFaker;

    /**
     * Tests user registration form validation
     *
     * @group  user
     * @return void
    */
    public function test_user_registration_validation_errors()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->assertSee('Register')
                    ->press('.btn-primary')
                    ->waitFor('.invalid-feedback')
                    ->assertSee('The name field is required')
                    ->assertSee('The email field is required')
                    ->assertSee('The password field is required')
                    ->assertSee('The terms must be accepted');
        });
    }
}
