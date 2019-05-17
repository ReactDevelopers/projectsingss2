<?php

namespace Tests\Browser\Race;

use App\Race;
use App\User;
use App\Hotel;
use App\Client;
use Tests\DuskTestCase;
use Laravel\Dusk\Chrome;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\DatePicker;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RaceInvoiceValidationTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */

    use WithFaker;

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
    }

    /**
     * A test to check if a race invoice can not blank
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoiceDueDate()
    {
        $this->browse(function (Browser $browser) {
            $date = $this->faker->date('d/m/Y');
            $name = $this->faker->bs;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('race_code', $race_code)
                ->type('year', $year)
                ->type('name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->value('input[name=due_on]', '')
                ->press('@create-invoice')
                ->waitForText('Due date is required')
                ->assertSee('Due date is required')
                ->type('input[name=due_on]', $this->faker->bs)
                ->press('@create-invoice')
                ->waitForText('The due on is not a valid date')
                ->assertSee('The due on is not a valid date');
        });
    }

    /**
     * A test to check if a race invoice client name auto suggest
     *
     * @group  race
     * @return void
    */
    public function testClientNameAutoSuggestForClient(){

        $this->browse(function (Browser $browser) {
            $date = $this->faker->date('d/m/Y');
            $this->client = $this->_createOneClient();

            $name = $this->faker->bs;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);

            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('race_code', $race_code)
                ->type('year', $year)
                ->type('name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->keys('@client-name-search', $this->client->name)
                ->waitForText($this->client->name)
                ->assertSee($this->client->name);
        });
    }



    /**
     * A test to check if a race invoice hotel name auto suggest
     *
     * @group  race
     * @return void
    */
    public function testClientNameAutoSuggestForHotel(){

        $this->browse(function (Browser $browser) {
            $date = $this->faker->date('d/m/Y');
            $hotel_name = $this->_createOneHotel();
            $name = $this->faker->bs;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('race_code', $race_code)
                ->type('year', $year)
                ->type('name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->keys('@hotel-name-search', substr($hotel_name->name, 0, 3))
                ->waitForText($hotel_name->name)
                ->assertSee($hotel_name->name);
        });
    }

    /**
     * A test to check if a race invoice hotel name auto suggest
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoiceHotelBlankName(){

        $this->browse(function (Browser $browser) {
            $date = $this->faker->date('d/m/Y');
            $hotel_name = $this->_createOneHotel();
            $name = $this->faker->bs;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);

            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('race_code', $race_code)
                ->type('year', $year)
                ->type('name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->type('@hotel-name-search', '')
                ->press('@submit-invoice')
                ->waitForText('Hotel is required')
                ->assertSee('Hotel is required');
        });
    }

    /**
     * A test to check if a race invoice race name should accept only string value
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoiceRaceInputString(){

        $this->browse(function (Browser $browser) {
            $date = $this->faker->date('d/m/Y');
            $hotel_name = $this->_createOneHotel();
            $name = $this->faker->bs;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);

            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('race_code', $race_code)
                ->type('year', $year)
                ->type('name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->pause(1000)
                ->clickLink('reset')
                ->type('@race-name-search', $this->faker->numberBetween($min = 1000, $max = 9000))
                ->press('@submit-invoice')
                ->waitForText('Race is required')
                ->assertSee('Race is required');
        });
    }

    /**
     * A test to check List of currancy should open and it should be selectable
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoiceRaceCurrancy(){

        $this->browse(function (Browser $browser) {
            $date = $this->faker->date('d/m/Y');
            $hotel_name = $this->_createOneHotel();
            $name = $this->faker->bs;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);

            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('race_code', $race_code)
                ->type('year', $year)
                ->type('name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->click('@currency')
                ->assertSee('AED');
        });
    }

    /**
     * A test to check List of currancy should open and it should be selectable
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoiceRaceDate(){

        $this->browse(function (Browser $browser) {
            $date = $this->faker->date('d/m/Y');
            $name = $this->faker->bs;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);

            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('@race-code', $race_code)
                ->type('@year', $year)
                ->type('@name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->waitFor('@room-date')
                ->press('@submit-invoice')
                ->waitForText('Description is required')
                ->assertSee('Description is required')
                ->assertSee('Date is required')
                ->assertSee('Quantity is required')
                ->assertSee('Rate is required');
        });
    }

    /**
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoiceRaceDateRange(){

        $this->browse(function (Browser $browser) {
            $date = $this->faker->date('d/m/Y');
            $name = $this->faker->bs;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);

            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('@race-code', $race_code)
                ->type('@year', $year)
                ->type('@name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->waitFor('@invoice-description')
                ->type('@invoice-description', $this->faker->numberBetween($min = 1000, $max = 9000))
                ->value('@invoice-date', $this->faker->date('d/m/Y', $date))
                ->type('@invoice-qty', $this->faker->bs)
                ->type('@invoice-rate', $this->faker->bs)
                ->press('@submit-invoice')
                ->waitForText('Description is required')
                ->assertSee('Description is required')
                ->assertSee('Quantity only accept numeric values')
                ->assertSee('Rate only accept numeric values');
        });
    }

    /**
     * A test to check the total
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoiceTotal(){
        $this->browse(function (Browser $browser) {
            $name = $this->faker->bs;
            $qty = $this->faker->randomDigit;
            $rate = $this->faker->numberBetween($min = 100, $max = 500);
            $result = $qty * $rate;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('race_code', $race_code)
                ->type('year', $year)
                ->type('name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->waitFor('@room-date')
                ->value('@room-date', $this->faker->dateTimeBetween('+2 week', '+1 month')->format('d/m/Y'))
                ->type('@invoice-description', $this->faker->bs)
                ->type('@invoice-qty', $qty)
                ->type('@invoice-rate', $rate)
                ->assertSeeIn('.qty_rate_total','$'.number_format($result).'.00')
                ->click('@delete-invoice-rate');
        });
    }

    /**
     * A test to check the total
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoicePaymentDetails(){
        $this->browse(function (Browser $browser) {
            $date = $this->faker->date('d/m/Y');
            $name = $this->faker->bs;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('race_code', $race_code)
                ->type('year', $year)
                ->type('name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->waitForText('The race has been saved.')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->waitFor('@amount-due-invoice')
                ->press('@create-invoice')
                ->waitForText('Description is required')
                ->assertSee('Description is required')
                ->assertSee('Amount is required')
                ->assertSee('Due on is required')
                ->type('@amount-due-invoice', $this->faker->numberBetween(1, 100))
                ->press('@create-invoice')
                ->assertDontSeeIn('.text-right', 'Required');

        });
    }

    /**
     * A test to check the form validation
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoicePaymentForm(){
        $this->browse(function (Browser $browser) {
            $date = $this->faker->date('d/m/Y');
            $name = $this->faker->bs;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('race_code', $race_code)
                ->type('year', $year)
                ->type('name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->waitFor('@amount-due-invoice')
                ->type('@amount-due-invoice', $this->faker->bs)
                ->press('@create-invoice')
                ->waitForText('Amount must be number')
                ->assertSee('Amount must be number');
        });
    }


    /**
     * A test to check details of the payment is deleted
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoicePaymentDelete(){

        $this->browse(function (Browser $browser) {
            $date = $this->faker->date('d/m/Y');
            $name = $this->faker->bs;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $year = $this->faker->numberBetween(2019, 2037);
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('race_code', $race_code)
                ->type('year', $year)
                ->type('name', $name)
                ->value('input[name=start_on]', date('d/m/Y', strtotime('+1 months')))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+2 months')))
                ->select('currency_id', $this->faker->numberBetween(1, 3))
                ->press('@race-submit')
                ->assertSee('The race has been saved.')
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->waitFor('@payment-name')
                ->type('@payment-name', $this->faker->bs)
                ->type('@amount-due-invoice', $this->faker->numberBetween(1, 30))
                ->value('@due-on', date('d/m/Y'))
                ->type('@amt-paid', $this->faker->numberBetween(1, 30))
                ->value('@paid-on', date('d/m/Y'))
                ->click('@delete-payment')
                ->assertDontSee('@payment-name');
        });
    }

    /**
     * A test to check details of the payment is deleted
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoiceCancel(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $race_id = $race->id;
            $browser->loginAs($this->user)
                ->visit('/races/'.$race_id)
                ->assertSee($race->name)
                ->press('@add-invoice')
                ->assertSee('Create a New Invoice')
                ->click('@cancel-race-invoice')
                ->assertUrlIs(url()->current().'/races/'.$race_id);
        });
    }


    /**
     * A test to check race invoice save
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoiceCreate(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $race_id = $race->id;
            $client = $this->_createOneClient();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race_id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->waitForText($hotel->name)
                ->assertSee($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText($hotel->name)
                ->assertSee($hotel->name)
                ->press('@add-extras-invoice')
                ->assertSee('Create a New Invoice')
                ->waitFor('@room-date')
                ->waitFor('@payment-name')
                ->click('input[name="due_on"]')
                ->waitFor('.vdpInnerWrap .vdpTable')
                ->click('.vdpInnerWrap .vdpTable tr:last-child td:last-child')
                ->type('@client-name-search', substr($client->name, 0,3))
                ->waitForText($client->name)
                ->assertSee($client->name)
                ->clickLink($client->name)
                ->click('@curr_4')
                ->click('@room-date')
                ->waitFor('.vdpInnerWrap .vdpTable')
                ->click('.vdpInnerWrap .vdpTable tr:last-child td:last-child')
                ->type('@invoice-description', $this->faker->bs)
                ->type('@invoice-qty', $this->faker->numberBetween(1, 30))
                ->type('@invoice-rate', $this->faker->numberBetween(1, 30))
                ->type('@payment-name', $this->faker->bs)
                ->type('@amount-due-invoice', $this->faker->numberBetween(1, 100))
                ->click('@due-on')
                ->waitFor('.vdpInnerWrap .vdpTable')
                ->click('.vdpInnerWrap .vdpTable tr:last-child td:last-child')
                ->type('@amt-paid', $this->faker->numberBetween(1, 100))
                ->click('@paid-on')
                ->waitFor('.vdpInnerWrap .vdpTable')
                ->click('.vdpInnerWrap .vdpTable tr:last-child td:last-child')
                ->press('@create-invoice')
                ->waitForText('The Extra Invoice has been saved.')
                ->assertSee('The Extra Invoice has been saved.');
        });
    }


    /**
     * A test to check race invoice to date
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoiceTodate(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $race_id = $race->id;
            $client = $this->_createOneClient();
            $hotel = $this->_createOneHotel();
            $to_date = $this->faker->dateTimeBetween('+1 week', '+1 month')->format('d/m/Y');
            $browser->loginAs($this->user)
                ->visit('/races/'.$race_id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->waitForText($hotel->name)
                ->assertSee($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText($hotel->name)
                ->assertSee($hotel->name)
                ->press('@add-extras-invoice')
                ->assertSee('Create a New Invoice')
                ->waitFor('@to-accounts-on')
                ->value('@to-accounts-on', $to_date)
                ->assertInputValue('@to-accounts-on', $to_date);
        });
    }

    /**
     * A test to check race invoice payment invoice date
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoicePaymentInvoiceDate(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $race_id = $race->id;
            $client = $this->_createOneClient();
            $hotel = $this->_createOneHotel();
            $payment_invoice_date = $this->faker->dateTimeBetween('+1 week', '+1 month')->format('d/m/Y');
            $browser->loginAs($this->user)
                ->visit('/races/'.$race_id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0, 4))
                ->waitForText($hotel->name)
                ->assertSee($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText($hotel->name)
                ->assertSee($hotel->name)
                ->press('@add-extras-invoice')
                ->assertSee('Create a New Invoice')
                ->waitFor('@payment-invoice-date')
                ->value('@payment-invoice-date', $payment_invoice_date)
                ->assertInputValue('@payment-invoice-date', $payment_invoice_date);
        });
    }


    /**
     * A test to check race invoice number
     *
     * @group  race
     * @return void
    */
    public function testRaceInvoicePaymentInvoiceNumber(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $race_id = $race->id;
            $client = $this->_createOneClient();
            $hotel = $this->_createOneHotel();
            $invoice_no = $this->faker->numberBetween($min = 1000, $max = 9000);
            $browser->loginAs($this->user)
                ->visit('/races/'.$race_id)
                ->assertSee($race->name)
                ->press('@add-race-hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->waitForText($hotel->name)
                ->assertSee($hotel->name)
                ->clickLink($hotel->name)
                ->waitForText($hotel->name)
                ->assertSee($hotel->name)
                ->press('@add-extras-invoice')
                ->assertSee('Create a New Invoice')
                ->waitFor('@invoice-no')
                ->type('@invoice-no', $invoice_no)
                ->assertInputValue('@invoice-no', $invoice_no);
        });
    }
}
