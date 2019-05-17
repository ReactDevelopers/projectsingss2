<?php

namespace Tests\Browser\tests\Browser\Roomimg;

use App\Race;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoomingManagementTest extends DuskTestCase
{
    use WithFaker;
    /**
     * A test to check redirect
     *
     * @return void
     * @group rooming-management
    */
    public function testRoomingList()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            Log::info($race);
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Edit Room Types and Rates')
                ->assertSee($hotel->name)
                ->assertSee('Room Types and Rates')
                ->select('inventory_currency_id', 5)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->clickLink('Add Line')
                ->type('@room-type-name', $this->faker->name)
                ->type('@min_night_hotel_rate', $this->faker->randomDigit)
                ->type('@min_night_client', $this->faker->randomDigit)
                ->type('@min_stays_contracted', $this->faker->randomDigit)
                ->type('@pre_post_night_hotel', $this->faker->randomDigit)
                ->type('@pre_post_night_client_rate', $this->faker->randomDigit)
                ->type('@pre_post_nights_contracted', $this->faker->randomDigit)
                ->press('@room-type-rate-submit')
                ->pause(5000)
                ->assertPresent('.alert-success');
        });
    }

    /**
     * A test to check date on edit rooming and confirmed on
     *
     * @return void
     * @group rooming-management
    */
    public function testRoomingDateCheck()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $list_sent_on = date('d/m/Y');
            $expected_list_sent_on = date("D").', '.date("M").' '.date("d").', '.date("Y");
            $list_confirmed_on = date('d/m/Y', strtotime('+1 days'));
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Edit Room Types and Rates')
                ->assertSee($hotel->name)
                ->assertSee('Room Types and Rates')
                ->select('inventory_currency_id', 5)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->clickLink('Add Line')
                ->type('@room-type-name', $this->faker->name)
                ->type('@min_night_hotel_rate', $this->faker->randomDigit)
                ->type('@min_night_client', $this->faker->randomDigit)
                ->type('@min_stays_contracted', $this->faker->randomDigit)
                ->type('@pre_post_night_hotel', $this->faker->randomDigit)
                ->type('@pre_post_night_client_rate', $this->faker->randomDigit)
                ->type('@pre_post_nights_contracted', $this->faker->randomDigit)
                ->press('@room-type-rate-submit')
                ->pause(5000)
                ->assertPresent('.alert-success')
                ->clickLink('Edit Rooming List')
                ->pause(5000)
                ->type('input[name=list_sent_on]', $list_sent_on)
                ->type('input[name=list_confirmed_on]', $list_confirmed_on)
                ->press('@save-rooming-list')
                ->assertInputValue('input[name=list_sent_on]', $list_sent_on)
                ->assertInputValue('input[name=list_confirmed_on]', $list_confirmed_on)
                ->waitForReload()
                ->visit(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->assertSee('List Confirmed On:')
                ->assertSee('List Sent On:')
                ->assertSee($expected_list_sent_on);
        });
    }



    /**
     * A test to check date on edit rooming and confirmed on
     *
     * @return void
     * @group rooming-management
    */
    public function testRoomingAfterSubmitRedirect()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $expected_list_sent_on = date("D").', '.date("M").' '.date("d").', '.date("Y");
            $list_sent_on = date('d/m/Y');
            $list_confirmed_on = date('d/m/Y', strtotime('+1 days'));
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Edit Room Types and Rates')
                ->assertSee($hotel->name)
                ->assertSee('Room Types and Rates')
                ->select('inventory_currency_id', 5)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->clickLink('Add Line')
                ->type('@room-type-name', $this->faker->name)
                ->type('@min_night_hotel_rate', $this->faker->randomDigit)
                ->type('@min_night_client', $this->faker->randomDigit)
                ->type('@min_stays_contracted', $this->faker->randomDigit)
                ->type('@pre_post_night_hotel', $this->faker->randomDigit)
                ->type('@pre_post_night_client_rate', $this->faker->randomDigit)
                ->type('@pre_post_nights_contracted', $this->faker->randomDigit)
                ->press('@room-type-rate-submit')
                ->pause(5000)
                ->assertPresent('.alert-success')
                ->clickLink('Edit Rooming List')
                ->pause(5000)
                ->type('input[name=list_sent_on]', $list_sent_on)
                ->type('input[name=list_confirmed_on]', $list_confirmed_on)
                ->press('@save-rooming-list')
                ->assertInputValue('input[name=list_sent_on]', $list_sent_on)
                ->assertInputValue('input[name=list_confirmed_on]', $list_confirmed_on)
                ->waitForReload()
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/reservations');
        });
    }


    /**
     * A test to check cancel
     *
     * @return void
     * @group rooming-management
    */
    public function testRoomingCancel()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $list_sent_on = date('d/m/Y');
            $expected_list_sent_on = date("D").', '.date("M").' '.date("d").', '.date("Y");
            $list_confirmed_on = date('d/m/Y', strtotime('+1 days'));
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Edit Room Types and Rates')
                ->assertSee($hotel->name)
                ->assertSee('Room Types and Rates')
                ->select('inventory_currency_id', 5)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->clickLink('Add Line')
                ->type('@room-type-name', $this->faker->name)
                ->type('@min_night_hotel_rate', $this->faker->randomDigit)
                ->type('@min_night_client', $this->faker->randomDigit)
                ->type('@min_stays_contracted', $this->faker->randomDigit)
                ->type('@pre_post_night_hotel', $this->faker->randomDigit)
                ->type('@pre_post_night_client_rate', $this->faker->randomDigit)
                ->type('@pre_post_nights_contracted', $this->faker->randomDigit)
                ->press('@room-type-rate-submit')
                ->pause(5000)
                ->assertPresent('.alert-success')
                ->clickLink('Edit Rooming List')
                ->pause(5000)
                ->type('input[name=list_sent_on]', $list_sent_on)
                ->type('input[name=list_confirmed_on]', $list_confirmed_on)
                ->press('@save-rooming-list')
                ->assertInputValue('input[name=list_sent_on]', $list_sent_on)
                ->assertInputValue('input[name=list_confirmed_on]', $list_confirmed_on)
                ->waitForReload()
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/reservations')
                ->clickLink('Cancel')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id);
        });
    }



}
