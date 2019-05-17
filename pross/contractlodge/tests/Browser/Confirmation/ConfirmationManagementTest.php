<?php

namespace Tests\Browser\Hotel;

use App\Race;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConfirmationManagementTest extends DuskTestCase
{
    use WithFaker;

    /**
     * A test to check add room confimation link is clickable
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationClickable()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create');
        });
    }


    /**
     * A test to check add room confimation link is clickable
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationInputVisible()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->assertSee('Expires On')
                ->assertSee('Client')
                ->assertSee('Currency')
                ->assertSee('Hotel')
                ->assertSee('Race');
        });
    }

    /**
     * A test to check add room confimation expire date >= current year
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationInputCheck()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 4 ))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->value('input[name=due_on]', $this->faker->date('22/07/1990'))
                ->assertSee('Expire date must be greater than current year');
        });
    }


    /**
     * A test to check add room confimation expire date >= current year
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationAutoSuggest()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $client = $this->_createOneClient();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->type('@client-name-search', $client->name)
                ->waitForLink($client->name)
                ->assertSee($client->name)
                ->assertSee($hotel->name)
                ->assertSee($race->name)
                ->assertSee('Minimum Nights:')
                ->assertPresent('@room-name')
                ->assertPresent('@room-quantity')
                ->assertPresent('@confirmation-item-check-in')
                ->assertPresent('@confirmation-item-check-out')
                ->assertPresent('@room-rate')
                ->type('@room-quantity', $this->faker->bs)
                ->assertSee('Rooms must contain only numbers')
                ->type('@room-quantity', $this->faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = NULL))
                ->assertSee('Rooms must contain only numbers')
                ->value('@confirmation-item-check-in', $this->faker->dateTimeBetween('-2 week', '-1 month')->format('d/m/Y'))
                ->assertSee('Check in date must be greater than current date')
                ->value('@confirmation-item-check-out', $this->faker->dateTimeBetween('-2 week', '-1 month')->format('d/m/Y'))
                ->assertSee('Check out date must be greater than current date');
        });
    }



    /**
     * A test to check add room confimation days count
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationDaysCount()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $client = $this->_createOneClient();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->value('@confirmation-item-check-in', $this->faker->dateTimeBetween('+1 week', '+1 month')->format('d/m/Y'))
                ->value('@confirmation-item-check-out', $this->faker->dateTimeBetween('+2 week', '+1 month')->format('d/m/Y'))
                ->assertSee('(5)');
        });
    }


    /**
     * A test to check add room confimation room rate
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationRoomRate()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $client = $this->_createOneClient();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->type('@room-rate', $this->faker->bs)
                ->assertSee('Room rate must be number');
        });
    }

    /**
     * A test to check add room confimation calculate total
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationCalculateTotal()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $client = $this->_createOneClient();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $room_qty = $this->faker->randomDigit;
            $room_rate = $this->faker->numberBetween($min = 1, $max = 50);
            $date_diff = date_diff(date_create($race->start_on) , date_create($race->end_on)); //differnce check-in & check-out
            $date_diff = $date_diff->days;
            $total = $room_rate * $room_qty * $date_diff;
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->select('@room-name', $room_type_name)
                ->type('@room-quantity', $room_qty)
                ->type('@room-rate', $room_rate)
                ->assertSee('$'.$total.'.00');

        });
    }


     /**
     * A test to check close room configuration action
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationCloseAction()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $client = $this->_createOneClient();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $room_qty = $this->faker->randomDigit;
            $room_rate = $this->faker->numberBetween($min = 1, $max = 50);
            $date_diff = 5; //differnce check-in & check-out
            $total = $room_rate * $room_qty * $date_diff;
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->select('@room-name', trim($room_type_name))
                ->type('@room-quantity', $room_qty)
                ->value('@confirmation-item-check-in', $this->faker->dateTimeBetween('+1 week', '+1 month')->format('d/m/Y'))
                ->value('@confirmation-item-check-out', $this->faker->dateTimeBetween('+2 week', '+1 month')->format('d/m/Y'))
                ->type('@room-rate', $room_rate)
                ->assertSee('$'.$total.'.00')
                ->click('@close-room-configuration')
                ->pause(1000)
                ->click('@add-line-room-configuration')
                ->assertNotSelected('@room-name', $room_type_name)
                ->assertInputValueIsNot('@room-quantity', $room_qty)
                ->assertInputValueIsNot('@room-rate', $room_rate);

        });
    }

    /**
     * A test to check payment schedule input
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationpaymentShedule()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $client = $this->_createOneClient();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $room_qty = $this->faker->randomDigit;
            $room_rate = $this->faker->numberBetween($min = 1, $max = 50);
            $date_diff = 5; //differnce check-in & check-out
            $total = $room_rate * $room_qty * $date_diff;
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->select('@room-name', trim($room_type_name))
                ->type('@room-quantity', $room_qty)
                ->value('@confirmation-item-check-in', $this->faker->dateTimeBetween('+1 week', '+1 month')->format('d/m/Y'))
                ->value('@confirmation-item-check-out', $this->faker->dateTimeBetween('+2 week', '+1 month')->format('d/m/Y'))
                ->type('@room-rate', $room_rate)
                ->assertSee('$'.$total.'.00')
                ->assertSee('Payment Schedule')
                ->assertPresent('@payment-name')
                ->assertPresent('@amount-due-invoice')
                ->assertPresent('@payment-name')
                ->assertPresent('@due-on')
                ->assertPresent('@amt-paid')
                ->assertPresent('@paid-on')
                ->assertPresent('@to_accounts_on')
                ->assertPresent('@invoice_number')
                ->assertPresent('@invoice_date');

        });
    }


    /**
     * A test to check payment schedule input validation
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationpaymentSheduleInputValidation()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $client = $this->_createOneClient();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $payment_name  = $this->faker->bs;
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->assertSee('Payment Schedule')
                ->assertPresent('@payment-name')
                ->assertPresent('@amount-due-invoice')
                ->assertPresent('@payment-name')
                ->assertPresent('@due-on')
                ->assertPresent('@amt-paid')
                ->assertPresent('@paid-on')
                ->assertPresent('@to_accounts_on')
                ->assertPresent('@invoice_number')
                ->assertPresent('@invoice_date')
                ->type('@amount-due-invoice', $this->faker->bs)
                ->press('@create-invoice')
                ->assertSee('Amount due must be number')
                ->type('@payment-name', $payment_name)
                ->press('@create-invoice')
                ->assertInputValue('@payment-name', $payment_name)
                ->value('@due-on', date('d/m/Y', strtotime('-10 years')))
                ->press('@create-invoice')
                ->assertSee('Amount due on date must >= 2018')
                ->type('@amt-paid', $this->faker->bs)
                ->press('@create-invoice')
                ->assertSee('Amount paid must be number type')
                ->value('@paid-on', date('d/m/Y', strtotime('-10 years')))
                ->press('@create-invoice')
                ->assertSee('Amount due on date must >= 2018');

        });
    }



    /**
     * A test to check payment schedule delete payment
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationDelete()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $client = $this->_createOneClient();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $payment_name  = $this->faker->bs;
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->waitForText('Payment Schedule')
                ->type('@payment-name', $payment_name)
                ->click('@delete-payment')
                ->click('@add-line-payment')
                ->assertInputValueIsNot('@payment-name', $payment_name);

        });
    }


    /**
     * A test to check payment schedule delete payment
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationCancil()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $client = $this->_createOneClient();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $payment_name  = $this->faker->bs;
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForText('Room Types and Rates have been saved.')
                ->assertSee('Room Types and Rates have been saved.')
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->click('@cancel-race-invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id);

        });
    }


    /**
     * A test to check payment confirmation save
     *
     * @return void
     * @group confirmation-management
    */
    public function testAddRoomConfirmationSave()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $client = $this->_createOneClient();
            $room_type_name = $this->faker->bs;
            $min_night_hotel_rate = $this->faker->numberBetween($min = 1, $max = 9);
            $min_night_client = $this->faker->numberBetween($min = 1, $max = 9);
            $min_stays_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $pre_post_night_hotel = $this->faker->numberBetween($min = 1, $max = 10);
            $pre_post_night_client_rate = $this->faker->numberBetween($min = 1, $max = 100);
            $pre_post_nights_contracted = $this->faker->numberBetween($min = 1, $max = 5);
            $payment_name  = $this->faker->bs;

            $room_qty = $this->faker->numberBetween($min = 1, $max = 2);;
            $room_rate = $this->faker->numberBetween($min = 1, $max = 50);

            $date_diff = date_diff(date_create($race->start_on) , date_create($race->end_on)); //differnce check-in & check-out
            $date_diff = $date_diff->days;
            $total = $room_rate * $room_qty * $date_diff;

            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 3))
                ->waitForLink($hotel->name)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText('Room Types and Rates')
                ->assertSee('Room Types and Rates')
                ->clickLink(' Edit Room Types and Rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(1000)

                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit')
                ->waitForReload()
                ->click('@add-room-confirmation')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/confirmations/create')
                ->type('input[name=due_on]', date('d/m/Y'))
                ->type('@client-name-search', substr($client->name, 0, 3))
                ->assertSee($client->name)
                ->clickLink($client->name)
                ->click('@curr_4')
                ->click('@'.$room_type_name)
                ->type('@room-quantity', $room_qty)
                ->type('@room-rate', $room_rate)
                ->type('@payment-name', $payment_name)
                ->type('@amount-due-invoice', $this->faker->numberBetween($min = 1000, $max = 9000))
                ->type('@amt-paid', $this->faker->numberBetween($min = 1000, $max = 9000))
                ->type('@due-on', date('d/m/Y', strtotime('+1 days')))
                ->type('@amt-paid', $this->faker->numberBetween($min = 1000, $max = 9000))
                ->value('@paid-on', date('d/m/Y', strtotime('+1 days')))
                ->value('@to-accounts-on', date('d/m/Y', strtotime('+1 days')))
                ->type('@invoice-no', $this->faker->numberBetween($min = 10, $max = 90))
                ->value('@payment-invoice-date', date('d/m/Y'))

                ->click('@create-invoice')
                ->waitForReload()
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id);
        });
    }


}

