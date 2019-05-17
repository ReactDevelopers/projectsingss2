<?php

namespace Tests\Feature;

use Tests\LaravelTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleLaravelTest extends LaravelTestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $this->assertContains('Laravel', $response->getContent());
    }
}
