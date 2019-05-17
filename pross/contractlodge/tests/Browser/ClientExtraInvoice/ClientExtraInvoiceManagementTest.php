<?php
namespace Tests\Browser\ClientExtraInvoice;

use App\Race;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class ClientExtraInvoiceManagementTest extends DuskTestCase
{
    use WithFaker;
    /**
     * A test to check redirect
     *
     * @return void
     * @group client-extra-invoice
    */

    public function testRedirectOnClickExtraInvoiceBtn()
    {

        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create');
        });
    }


    /**
     * A test to check various input fields
     *
     * @return void
     * @group client-extra-invoice
    */

    public function testClientExtraInvoiceInputVisible()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create')
                ->waitFor('@room-date')
                ->waitFor('@payment-name')
                ->assertVisible('@client-name-search')
                ->assertVisible('@currency')
                ->assertVisible('@room-date')
                ->assertVisible('@invoice-description')
                ->assertVisible('@invoice-qty')
                ->assertVisible('@invoice-rate')
                ->assertVisible('@additional-notes')
                ->assertVisible('@payment-name')
                ->assertVisible('@amount-due-invoice')
                ->assertVisible('@amt-paid')
                ->assertVisible('@create-invoice');
        });
    }

    /**
     * A test to check due date
     *
     * @return void
     * @group client-extra-invoice
    */
    public function testClientExtraInvoiceDueDateAndCurrency()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create')
                ->clear('input[name=due_on]')
                ->value('select[name=currency]', '')
                ->press('@create-invoice')
                ->waitForText('Due date is required')
                ->assertSee('Due date is required')
                ->assertSee('Currency is required')
                ->type('input[name=due_on]', $this->faker->bs)
                ->press('@create-invoice')
                ->waitForText('The due on is not a valid date')
                ->assertSee('The due on is not a valid date');

        });
    }


    /**
     * A test to check client input
     *
     * @return void
     * @group client-extra-invoice
    */
    public function testClientExtraInvoiceClientInput()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create')
                ->press('@create-invoice')
                ->waitForText('Client is required')
                ->assertSee('Client is required');
        });
    }

    /**
     * A test to check client hotel details present
     *
     * @return void
     * @group client-extra-invoice
    */
    public function testClientExtraInvoiceHotelDetails()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create')
                ->assertSee($hotel->name);
        });
    }

    /**
     * A test to check client hotel details present
     *
     * @return void
     * @group client-extra-invoice
    */
    public function testClientExtraInvoiceRaceDetails()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create')
                ->assertSee($race->year.' '.$race->name);
        });
    }

    /**
     * A test to check room date
     *
     * @return void
     * @group client-extra-invoice
    */
    public function testClientExtraInvoiceRoomDate()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create')
                ->waitFor('@room-date')
                ->clear('@room-date')
                ->press('@create-invoice')
                ->waitForText('Date is required')
                ->assertSee('Date is required');
        });
    }


    /**
     * A test to check room date
     *
     * @return void
     * @group client-extra-invoice
    */
    public function testClientExtraInvoiceDescriptionInput()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $desc = $this->faker->bs;
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create')
                ->waitFor('@invoice-description')
                ->clear('@invoice-description')
                ->press('@create-invoice')
                ->waitForText('Description is required')
                ->assertSee('Description is required')
                ->type('@invoice-description', $desc)
                ->press('@create-invoice')
                ->assertInputValue('@invoice-description', $desc);
        });
    }

    /**
     * A test to check room date
     *
     * @return void
     * @group client-extra-invoice
    */
    public function testClientExtraInvoiceQtyInput()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $invoice_qty = $this->faker->randomDigit;
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create')
                ->waitFor('@invoice-qty')
                ->clear('@invoice-qty')
                ->press('@create-invoice')
                ->waitForText('Quantity is required')
                ->assertSee('Quantity is required')
                ->type('@invoice-qty', $invoice_qty)
                ->press('@create-invoice')
                ->assertInputValue('@invoice-qty', $invoice_qty);
        });
    }

    /**
     * A test to check room date
     *
     * @return void
     * @group client-extra-invoice
    */
    public function testClientExtraInvoiceQtyInputNumber()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create')
                ->waitFor('@invoice-qty')
                ->clear('@invoice-qty')
                ->press('@create-invoice')
                ->waitForText('Quantity is required')
                ->assertSee('Quantity is required')
                ->type('@invoice-qty', $this->faker->bs)
                ->press('@create-invoice')
                ->waitForText('Quantity only accept numeric values')
                ->assertSee('Quantity only accept numeric values');
        });
    }


    /**
     * A test to check Rate input accepts number
     *
     * @return void
     * @group client-extra-invoice
    */
    public function testClientExtraInvoiceRateInputNumber()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $invoice_rate = $this->faker->randomDigit;
            Log::info('/races/'.$race->id.'/hotels/'.$hotel->id);
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create')
                ->waitFor('@invoice-rate')
                ->clear('@invoice-rate')
                ->press('@create-invoice')
                ->waitForText('Rate is required')
                ->assertSee('Rate is required')
                ->type('@invoice-rate', $this->faker->bs)
                ->press('@create-invoice')
                ->waitForText('Rate only accept numeric values')
                ->assertSee('Rate only accept numeric values');
        });
    }

    /**
     * A test to check payment schecule input fields
     *
     * @return void
     * @group client-extra-invoice
    */
    public function testClientExtraInvoicePaymentInputs()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', substr($hotel->name, 0,4))
                ->waitForLink($hotel->name)
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->clickLink('Add Extras Invoice')
                ->waitForText('Create a New Invoice')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/invoices/extras/create')
                ->waitForText('Payment Schedule')
                ->waitFor('@payment-name')
                ->clear('@payment-name')
                ->clear('@amount-due-invoice')
                ->clear('@due-on')
                ->press('@create-invoice')
                ->waitForText('Description is required')
                ->assertSeeIn('@payment-schedule-container', 'Description is required')
                ->assertSeeIn('@payment-schedule-container', 'Amount is required')
                ->assertSeeIn('@payment-schedule-container', 'Due on is required')
                ->type('@amount-due-invoice', $this->faker->bs)
                ->type('@due-on', $this->faker->bs)
                ->type('@to-accounts-on', $this->faker->bs)
                ->type('@payment-invoice-date', $this->faker->bs)
                ->press('@create-invoice')
                ->waitForText('Amount must be number')
                ->assertSeeIn('@payment-schedule-container', 'Amount must be number')
                ->assertSeeIn('@due-on-container', 'Select valid date')
                ->assertSeeIn('@to-accounts-on-container', 'Select valid date')
                ->assertSeeIn('@payment-invoice-date-container', 'Select valid date');
        });
    }
}
