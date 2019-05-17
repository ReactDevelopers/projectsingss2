<?php

namespace Tests\Browser\Race;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RaceHotelCreateTest extends DuskTestCase
{
    use WithFaker;

    /**
     * A test to check if a hotel can be created successfully with correct fields
     *
     * @group  race
     * @return void
    */
    public function testCreateHotel()
    {
        $user = $this->_createOneUser();
        $race = $this->_createOneRace();

        $this->browse(function (Browser $browser) use ($user, $race) {
            $browser->loginAs($user);
            $name = $this->faker->bs;
            $browser->visit('/home')
                ->click('@race-'.$race->id)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->press('@add-hotel')
                ->type('@name', $name)
                ->type('@address', $this->faker->streetAddress)
                ->type('@city', $this->faker->city)
                ->type('@region', $this->faker->stateAbbr)
                ->select('@country_id', $this->faker->numberBetween(1, 21))
                ->type('@postal_code', $this->faker->numberBetween(88888, 99999))
                ->type('@website', $this->faker->url)
                ->type('@note', $this->faker->paragraph($nbSentences = 3, $variableNbSentences = true))
                ->press('@hotel-submit')
                ->waitFor('.alert-success')
                ->assertPresent('.alert-success');
        });
    }
}
