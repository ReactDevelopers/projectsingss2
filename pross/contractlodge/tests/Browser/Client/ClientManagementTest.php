<?php

namespace Tests\Browser;

use App\User;
use App\Client;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClientArchiveTest extends DuskTestCase
{
    use WithFaker;

    /**
     * A test to check if a client can be created successfully with correct fields
     *
     * @group  client
     * @return void
    */
    public function testCreateClient()
    {
        $this->browse(function (Browser $browser) {
            $name = $this->faker->bs;
            $browser->loginAs($this->user)
                ->visit('/clients')
                ->assertSee('Active Clients')
                ->press('@add-client')
                ->assertSee('Add Client')
                ->type('@client-name', $name)
                ->type('@client-address', $this->faker->streetAddress)
                ->type('@client-city', $this->faker->city)
                ->type('@client-email', $this->faker->unique()->safeEmail)
                ->type('@client-phone', $this->faker->tollFreePhoneNumber)
                ->type('@client-region', $this->faker->stateAbbr)
                ->select('@client-country', $this->faker->numberBetween(1, 21))
                ->type('@client-postal-code', $this->faker->postcode)
                ->type('@client-email', $this->faker->unique()->safeEmail)
                ->type('@client-phone', $this->faker->phoneNumber)
                ->type('@client-website', $this->faker->domainName)
                ->press('@client-submit')
                ->waitForReload()
                ->assertSee($name)
                ->assertPresent('.alert-success');
        });
    }


    /**
     * A test to check if a client can be created successfully with correct fields
     *
     * @group  client
     * @return void
    */
    public function testClientCanBeEdited()
    {
        $this->client = $this->_createOneClient();
        $this->browse(function (Browser $browser) {
            $name = $this->client->name;
            $address = $this->client->address;
            $city = $this->client->city;
            $region = $this->client->region;
            $postal_code = $this->client->postal_code;
            $country_id = $this->client->country_id;
            $phone = $this->client->phone;
            $email = $this->client->email;
            $website = $this->client->website;

            $browser->loginAs($this->user)
                ->visit('/clients/' . $this->client->id . '/')
                ->assertSee($name)
                ->press('@client-edit')
                ->assertSee($name)
                ->type('@client-name', $this->faker->bs)
                ->type('@client-address', $this->faker->streetAddress)
                ->type('@client-city', $this->faker->city)
                ->type('@client-region', $this->faker->stateAbbr)
                ->select('@client-country', $this->faker->numberBetween(2, 21))
                ->type('@client-postal-code', $this->faker->postcode)
                ->type('@client-email', $this->faker->unique()->safeEmail)
                ->type('@client-phone', $this->faker->tollFreePhoneNumber)
                ->type('@client-website', $this->faker->domainName)
                ->press('@client-submit')
                ->waitForReload()
                ->assertPresent('.alert-success')
                ->assertDontSee($name)
                ->assertDontSee($address)
                ->assertDontSee($city)
                ->assertDontSee($region)
                ->assertDontSee($postal_code)
                ->assertDontSee($phone)
                ->assertDontSee($email)
                ->assertDontSee($website);
        });
    }


    /**
     * A test to check if a client can be created successfully with correct fields
     *
     * @group  client
     * @return void
    */
    public function testClientCanBeArchived()
    {
        $this->client = $this->_createOneClient();
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/clients')
                ->assertSee($this->client->name)
                ->press('@client-archive')
                ->acceptDialog()
                ->assertPresent('.alert-success')
                ->assertDontSee($this->client->name);
        });
    }


    /**
     * A test to check if a client can be created successfully with correct fields
     * // deleted by and deleted at fields will archive objects
     *
     * @group  client
     * @return void
    */
    public function testClientCanBeUnarchived()
    {
        $this->client = $this->_createOneClient();
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/clients')
                ->assertSee($this->client->name)
                ->press('@client-archive')
                ->acceptDialog()
                ->assertPresent('.alert-success')
                ->assertDontSee($this->client->name)
                ->visit('/clients/archived')
                ->press('@client-unarchive')
                ->acceptDialog()
                ->assertPresent('.alert-success')
                ->assertDontSee($this->client->name);
        });
    }


}
