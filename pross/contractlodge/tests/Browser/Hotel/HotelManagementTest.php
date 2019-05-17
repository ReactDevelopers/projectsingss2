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

class HotelManagementTest extends DuskTestCase
{
    use WithFaker;


    /**
     * The user we will use to login and do stuff
     * @var App\User
    */
    public $user;

    /**
     * @var App\Hotel
    */
    public $hotel;

    /**
     * Setup the user for the tests
    */
    public function setUp()
    {
        parent::setUp();
        $this->user = $this->_createOneUser();
        $this->hotel = $this->_createOneHotel();
    }


    /**
     *
     * @test
     * @group hotel
     * @return void
     */
    public function testOpenHotel()
    {
        //dd(env('DB_CONNECTION'));
        //dd(env('APP_ENV'));
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel');
        });
    }

    /**
     * A test to open hotel create form.
     *
     * @group hotel
     * @return void
     *
     */
    public function testOpenHotelForm(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->click('@add-hotel')
                ->assertSee('Add Hotel to '.$race->year.' '.$race->name);
        });
    }

    /**
     * A test to open hotel name string
     *
     * @group hotel
     * @return void
     *
     */
    public function testOpenHotelNameString(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->click('@add-hotel')
                ->assertSee('Add Hotel to '.$race->year.' '.$race->name)
                ->type('@name', $this->faker->bs)
                ->press('@hotel-submit')
                ->assertDontSeeIn('@name', 'Please enter the valid name')
                ->click('@add-line-hotel-contact')
                ->type('@client-contact-name', $this->faker->bs)
                ->press('@hotel-submit')
                ->assertDontSeeIn('@name', 'Please enter the valid contact name')
                ->type('@client-contact-name', 14785226)
                ->press('@hotel-submit')
                ->waitForText('The contact name only contain characters')
                ->assertSee('The contact name only contain characters')
                ->type('@client-contact-name', '####$$$$')
                ->press('@hotel-submit')
                ->waitForText('The contact name only contain characters')
                ->assertSee('The contact name only contain characters');
        });
    }

    /**
     * A test to open hotel address
     *
     * @group hotel
     * @return void
     *
     */
    public function testOpenHotelAddress(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->click('@add-hotel')
                ->assertSee('Add Hotel to '.$race->year.' '.$race->name)
                ->type('@address', $this->faker->address)
                ->press('@hotel-submit')
                ->assertDontSeeIn('@address', '.is-invalid')
                ->clear('@address')->keys('@address', 'A', '{backspace}')
                ->press('@hotel-submit')
                ->type('@address', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.')
                ->press('@hotel-submit')
                ->waitForText('The address may not be greater than 350 characters.')
                ->assertSee('The address may not be greater than 350 characters.');
        });
    }


    /**
     * A test to verify hotel city
     *
     * @group hotel
     * @return void
     *
     */
    public function testOpenHotelCity(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->click('@add-hotel')
                ->assertSee('Add Hotel to '.$race->year.' '.$race->name)
                ->press('@hotel-submit')
                ->type('@city' , 7845112)
                ->press('@hotel-submit')
                ->waitForText('The city may only contain letters.')
                ->assertSee('The city may only contain letters.')
                ->type('@city' , 'Agra')
                ->press('@hotel-submit')
                ->assertDontSeeIn('@city', '.is-invalid');
                /*
                ->assertSee('City is required')
                ->type('@city' , 7845112)
                ->press('@hotel-submit')
                ->waitForText('The city may only contain letters.')
                ->assertSee('The city may only contain letters.')
                ->type('@city' , 'Agra')
                ->press('@hotel-submit')
                ->type('@contact-email', 'ranium')
                ->press('@hotel-submit')
                ->assertSee('Please enter valid email format')
                ->type('@contact-phone', 'phone no')
                ->press('@hotel-submit')
                ->assertSee('Contact phone accept only numbers')
                ->type('@contact-phone', $this->faker->bs)
                ->press('@hotel-submit')
                ->assertSee('Contact phone can not accept string')
                ->type('@contact-phone', '#$$%%')
                ->press('@hotel-submit')
                ->assertSee('Contact phone can not accept special characters');
                */
        });
    }

    /**
     * A test to verify hotel region
     *
     * @group hotel
     * @return void
     *
     */
    public function testOpenHotelRegion(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->click('@add-hotel')
                ->assertSee('Add Hotel to '.$race->year.' '.$race->name)
                ->type('@region', $this->faker->state)
                ->press('@hotel-submit')
                ->assertDontSee('Invalid state value')
                ->type('@region', '@#$%^&')
                ->press('@hotel-submit')
                ->waitForText('The region may only contain letters.')
                ->assertSee('The region may only contain letters.');
        });
    }


    /**
     * A test to verify hotel country
     *
     * @group hotel
     * @return void
     *
     */
    public function testOpenHotelCountry(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->click('@add-hotel')
                ->assertSee('Add Hotel to '.$race->year.' '.$race->name)
                ->select('@country_id', 3)
                ->press('@hotel-submit')
                ->assertDontSee('Invalid country');
        });
    }

    /**
     * A test to verify hotel postal code
     *
     * @group hotel
     * @return void
     *
     */
    public function testOpenHotelPostalCode(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->click('@add-hotel')
                ->assertSee('Add Hotel to '.$race->year.' '.$race->name)
                ->type('@postal_code', $this->faker->text($maxNbChars = 10))
                ->press('@hotel-submit')
                ->waitForText('The postal code must be an integer.')
                ->assertSee('The postal code must be an integer.')
                ->clear('@postal_code')->keys('@postal_code', 'A', '{backspace}')
                ->type('@postal_code', $this->faker->text($maxNbChars = 10))
                ->assertDontSee('postal code is invalid');
        });
    }

    /**
     * A test to verify hotel website
     *
     * @group hotel
     * @return void
     *
     */
    public function testOpenHotelWebsite(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->click('@add-hotel')
                ->assertSee('Add Hotel to '.$race->year.' '.$race->name)
                ->type('@website', $this->faker->bs)
                ->press('@hotel-submit')
                ->waitForText('The website format is invalid.')
                ->assertSee('The website format is invalid.');
        });
    }

    /**
     * A test to verify hotel note
     *
     * @group hotel
     * @return void
     *
     */
    public function testOpenHotelNote(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->click('@add-hotel')
                ->assertSee('Add Hotel to '.$race->year.' '.$race->name)
                ->type('@note', $this->faker->bs)
                ->press('@hotel-submit')
                ->assertDontSee('Please enter the valid note');
        });
    }

    /**
     * A test to verify hotel website
     *
     * @group hotel
     * @return void
     *
     */
    public function testOpenHotelCancel(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->click('@add-hotel')
                ->assertSee('Add Hotel to '.$race->year.' '.$race->name)
                ->press('@hotel-cancel')
                ->assertUrlIs(url()->current().'/races/'.$race->id);
        });
    }


    /**
     * A test to verify hotel website
     *
     * @group hotel
     * @return void
     *
     */
    public function testSearchHotel(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name);
        });
    }


    /**
     * A test to check if a race can be created successfully with correct fields
     *
     * @group hotel
     * @return void
    */
    public function testHotelCanBeCreated()
    {

        $this->browse(function (Browser $browser) {
            $name = $this->faker->bs;
            $browser->loginAs($this->user)
                ->visit('/hotels')
                ->assertSee('Active Hotels')
                ->press('@add-hotel')
                ->assertSee('Add Hotel')
                ->type('@name', $name)
                ->type('@address', $this->faker->streetAddress)
                ->type('@city', $this->faker->city)
                ->type('@region', $this->faker->stateAbbr)
                ->select('@country_id', $this->faker->numberBetween(1, 21))
                ->type('@postal_code', '78704')
                ->type('@website', 'https://'.$this->faker->domainName)
                ->type('@note', $this->faker->paragraph($nbSentences = 3, $variableNbSentences = true))
                ->press('@hotel-submit')
                ->pause(5000)
                ->assertPresent('.alert-success');
        });
    }


    /**
     * A test to check if a hotel can be created successfully with correct fields
     *
     * @group hotel
     * @return void
    */
    public function testHotelCanBeEdited()
    {
        $this->browse(function (Browser $browser) {
            $name = $this->hotel->name;
            $address = $this->hotel->address;
            $city = $this->hotel->city;
            $region = $this->hotel->region;
            $postal_code = $this->hotel->postal_code;
            $country_id = $this->hotel->country_id;
            $phone = $this->hotel->phone;
            $email = $this->hotel->email;
            $website = $this->hotel->website;
            $notes = $this->hotel->notes;
            Log::info($this->user);
            Log::info($this->hotel->id);
            $browser->loginAs($this->user)
                ->visit('/hotels/' . $this->hotel->id)
                ->assertSee($name)
                ->press('@hotel-edit')
                ->assertSee($name)
                ->type('@name', $this->faker->bs)
                ->type('@address', $this->faker->streetAddress)
                ->type('@city', $this->faker->city)
                ->type('@region', $this->faker->stateAbbr)
                ->select('@country_id', $this->faker->numberBetween(2, 21))
                ->type('@postal_code', '78704')
                ->type('@email', $this->faker->unique()->safeEmail)
                ->type('@phone', $this->faker->tollFreePhoneNumber)
                ->type('@website', 'https://'.$this->faker->domainName)
                ->type('@note', $this->faker->paragraph($nbSentences = 3, $variableNbSentences = true))
                ->click('@add-line-hotel-contact')
                ->type('@client-contact-name', 'David Armstrom')
                ->type('@contact-email', $this->faker->unique()->safeEmail)
                ->type('@contact-phone', $this->faker->tollFreePhoneNumber)
                ->type('@hotel-contact-role', 'Admin')
                ->press('@hotel-edit-submit')
                ->pause(5000)
                ->assertPresent('.alert-success')
                ->assertVisible('.alert-success')
                ->assertDontSee($name)
                ->assertDontSee($address)
                ->assertDontSee($city)
                ->assertDontSee($region)
                ->assertDontSee($postal_code)
                ->assertDontSee($phone)
                ->assertDontSee($email)
                ->assertDontSee($website)
                ->assertDontSee($notes);
        });
    }


    /**
     * A test to check if a hotel can be created successfully with correct fields
     *
     * @group hotel
     * @return void
    */
    public function testHotelCanBeArchived()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/hotels')
                ->assertSee($this->hotel->name)
                ->press('@hotel-archive')
                ->acceptDialog()
                ->assertPresent('.alert-success')
                ->assertDontSee($this->hotel->name);
        });
    }

    /**
     * A test to check hotel can be unarchived
     *
     * @group hotel
     * @return void
    */
    public function testHotelCanBeUnarchived()
    {
        $this->_createOneHotel();
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/hotels')
                ->assertSee($this->hotel->name)
                ->press('@hotel-archive')
                ->acceptDialog()
                ->assertPresent('.alert-success')
                ->visit('/hotels/archived')
                ->press('@hotel-unarchive')
                ->acceptDialog()
                ->assertPresent('.alert-success')
                ->assertDontSee($this->hotel->name);
        });
    }


    /**
     * A test to check if a hotel is removed from race
     *
     * @group hotel
     * @return void
    */
    public function testHotelCanBeRemovedFromRace()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(5000)
                ->press('@race-archive')
                ->acceptDialog()
                ->assertPresent('.alert-success')
                ->assertUrlIs(url()->current().'/races/'.$race->id);
        });
    }


    /* *******************Room Types and Rates Test Cases Start**************** */
    /**
     * A test to check room types and rates
     *
     * @group hotel
     * @return void
    */
    public function testHotelRoomTypeRate()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit');

        });
    }


    /**
     * A test to check room types and rates date check
     *
     * @group hotel
     * @return void
    */
    public function testHotelRoomTypeRateDateCheck()
    {

        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(10000)
                ->click('input[name="inventory_min_check_in"]')
                ->waitFor('.vdpInnerWrap .vdpTable')
                ->click('.vdpInnerWrap .vdpTable tr:last-child td:last-child')
                ->pause(10000)
                ->press('@room-type-rate-submit')
                ->pause(5000)
                ->waitForText('Minimum Check-in date must be smaller than Minimum Check-out date')
                ->assertSee('Minimum Check-in date must be smaller than Minimum Check-out date');
        });
    }


    /**
     * A test to check total period in hotel
     *
     * @group hotel
     * @return void
    */
    // COMMENTED THIS CODE DUE TO ISSUE https://github.com/dbrekalo/vue-date-pick/issues/9
    /*
    public function testHotelRoomTypeRatePeriodInHotel()
    {

        $this->browse(function (Browser $browser) {
            //dd($this->faker->date($format = 'd/m/Y'));
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->value('input[name=inventory_min_check_in]' , '12/03/2019')
                ->pause(5000)
                ->type('input[name=inventory_min_check_out]' , '15/03/2019')
                ->pause(5000)
                ->assertPresent('Tue, Mar 12, 2019 - Fri, Mar 15, 2019 (3 nights)');

        });
    }
    */


    /**
     * A test to check total race hotel edit note
     *
     * @group hotel
     * @return void
    */
    public function testRaceHotelEditInventory()
    {
        $this->browse(function (Browser $browser) {
            //dd($this->faker->date($format = 'd/m/Y'));
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->type('input[name=inventory_notes]' , $this->faker->bs)
                ->assertDontSee('Invalid note format')
                ->press('@room-type-rate-submit');
        });
    }


    /**
     * A test to check total race hotel edit room name
     *
     * @group hotel
     * @return void
    */
    public function testRaceHotelEditRoomName()
    {
        $this->browse(function (Browser $browser) {
            //dd($this->faker->date($format = 'd/m/Y'));
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(3000)
                ->press('@add-line')
                ->type('@room-type-name' , $this->faker->bs)
                ->press('@room-type-rate-submit')
                ->assertDontSee('Room type/name is required');
        });
    }

    /**
     * A test to check total race hotel edit Min/Nt Hotel
     *
     * @group hotel
     * @return void
    */
    public function testRaceHotelEditRoomMinNT()
    {
        $this->browse(function (Browser $browser) {
            //dd($this->faker->date($format = 'd/m/Y'));
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(3000)
                ->press('@add-line')
                ->pause(3000)
                ->type('@min-night-hotel-rate', $this->faker->bs)
                ->press('@room-type-rate-submit')
                ->waitForText('The min night hotel must be an number')
                ->assertSee('The min night hotel must be an number');
        });
    }


    /**
     * A test to check total race hotel edit Min/Nt Hotel
     *
     * @group hotel
     * @return void
    */
    public function testRaceHotelEditTotalRoomBooked()
    {
        $this->browse(function (Browser $browser) {
            //dd($this->faker->date($format = 'd/m/Y'));
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(3000)
                ->press('@add-line')
                ->type('@min_stays_contracted', 12)
                ->assertSee('12');
        });
    }

    /**
     * A test to check total race hotel edit Min/Nt Hotel
     *
     * @group hotel
     * @return void
    */
    public function testRaceHotelEditPrePost()
    {
        $this->browse(function (Browser $browser) {
            //dd($this->faker->date($format = 'd/m/Y'));
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(5000)
                ->press('@add-line')
                ->pause(5000)
                ->type('@pre_post_night_hotel', $this->faker->bs)
                ->press('@room-type-rate-submit')
                ->pause(5000)
                ->waitForText('The pre post night hotel must be an number')
                ->assertSee('The pre post night hotel must be an number');
        });
    }


    /**
     * A test to check total race hotel edit Min/Nt Hotel
     *
     * @group hotel
     * @return void
    */
    public function testRaceHotelEditPrePostNightsContracted()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(5000)
                ->press('@add-line')
                ->type('@min_stays_contracted', 39)
                ->assertSee('39');
        });
    }

    /**
     * A test to check total race hotel delete line
     *
     * @group hotel
     * @return void
    */
    public function testRaceHotelEditDeleteLine()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(5000)
                ->press('@add-line')
                ->type('@room-type-name', $this->faker->bs)
                ->type('@min-night-hotel-rate', $this->faker->numberBetween($min = 1, $max = 9))
                ->type('@min_night_client', $this->faker->numberBetween($min = 1, $max = 9))
                ->type('@min_stays_contracted', $this->faker->numberBetween($min = 1, $max = 5))
                ->type('@pre_post_night_hotel', $this->faker->numberBetween($min = 1, $max = 10))
                ->type('@pre_post_night_client_rate', $this->faker->numberBetween($min = 1, $max = 100))
                ->type('@pre-post-nights-contracted', $this->faker->numberBetween($min = 1, $max = 5))
                ->click('@delete-line')
                ->assertDontSeeLink('@delete-line');
        });
    }


    /**
     * A test to check total race hotel add line
     *
     * @group hotel
     * @return void
    */
    public function testRaceHotelEditAddMultipleLine()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(5000)
                ->press('@add-line')
                ->pause(5000)
                ->assertVisible('@room-type-name')
                ->press('@add-line')
                ->assertVisible('@room-type-name');
        });
    }

    /**
     * A test to check total race hotel add line
     *
     * @group hotel
     * @return void
    */
    public function testRaceHotelEditCancil()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->click('@room-type-rate-cancil')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id);
        });
    }

    /**
     * A test to check total race hotel add line
     *
     * @group hotel
     * @return void
    */
    public function testRaceHotelEdit()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(5000)
                ->type('input[name="inventory_min_check_in"]', $this->faker->dateTimeBetween('+1 week', '+1 month')->format('d/m/Y'))
                ->type('input[name="inventory_min_check_out"]', $this->faker->dateTimeBetween('+2 week', '+1 month')->format('d/m/Y'))
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $this->faker->bs)
                ->type('@min-night-hotel-rate', $this->faker->numberBetween($min = 1, $max = 9))
                ->type('@min_night_client', $this->faker->numberBetween($min = 1, $max = 9))
                ->type('@min_stays_contracted', $this->faker->numberBetween($min = 1, $max = 5))
                ->type('@pre_post_night_hotel', $this->faker->numberBetween($min = 1, $max = 10))
                ->type('@pre_post_night_client_rate', $this->faker->numberBetween($min = 1, $max = 100))
                ->type('@pre-post-nights-contracted', $this->faker->numberBetween($min = 1, $max = 5))
                ->press('@room-type-rate-submit');
        });
    }



    /* *******************Room Types and Rates Test Cases End************ */
    /*******************Client BreakDown Start***********************/
    /**
     * A test to check total race hotel add line
     *
     * @group hotel
     * @return void
    */
    public function testClientBreakdown()
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
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(5000)
                ->value('input[name="inventory_min_check_in"]', $this->faker->dateTimeBetween('+1 week', '+1 month')->format('d/m/Y'))
                ->value('input[name="inventory_min_check_out"]', $this->faker->dateTimeBetween('+1 week', '+3 month')->format('d/m/Y'))
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit');
        });
    }
    /*******************Client BreakDown End***********************/
    /******************Create Confirmation Start*******************/
    /**
     * A test to check total race hotel add line
     *
     * @group hotel
     * @return void
    */
    public function testCreateConfirmation()
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
                ->type('@hotel-name-search', $hotel->name)
                ->pause(5000)
                ->assertSeeLink($hotel->name)
                ->clickLink($hotel->name)
                ->pause(3000)
                ->assertSee('Room Types and Rates')
                ->click('@room-types-and-rates')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/edit')
                ->pause(5000)
                ->value('input[name="inventory_min_check_in"]', $this->faker->dateTimeBetween('+1 week', '+1 month')->format('d/m/Y'))
                ->value('input[name="inventory_min_check_out"]', $this->faker->dateTimeBetween('+1 week', '+3 month')->format('d/m/Y'))
                ->type('input[name=inventory_notes]', $this->faker->bs)
                ->press('@add-line')
                 ->type('@room-type-name', $room_type_name)
                ->type('@min-night-hotel-rate', $min_night_hotel_rate)
                ->type('@min_night_client', $min_night_client)
                ->type('@min_stays_contracted', $min_stays_contracted)
                ->type('@pre_post_night_hotel', $pre_post_night_hotel)
                ->type('@pre_post_night_client_rate', $pre_post_night_client_rate)
                ->type('@pre-post-nights-contracted', $pre_post_nights_contracted)
                ->press('@room-type-rate-submit');
        });
    }
    /******************Create Confirmation End*******************/
}