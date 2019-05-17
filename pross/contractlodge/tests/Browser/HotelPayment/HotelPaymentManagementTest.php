<?php

namespace Tests\Browser\Browsers\HotelPayment;

use App\Race;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HotelPaymentManagementTest extends DuskTestCase
{
    use WithFaker;
    /**
     * A test to check redirect
     *
     * @return void
     * @group hotel-payment
    */
    public function testHotelPaymentText()
    {
        $this->browse(function (Browser $browser) {
            $race  = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->waitFor('.list-group-item')
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->waitForText('Hotel Payments')
                ->assertSee('Hotel Payments');

        });
    }

    /**
     * A test to check redirect after btn click
     *
     * @return void
     * @group hotel-payment
    */
    public function testHotelPaymentBtnRedirect()
    {
        $this->browse(function (Browser $browser) {
            $race  = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->waitFor('.list-group-item')
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->waitForText('Hotel Payments')
                ->assertSee('Hotel Payments')
                ->click('@hotel-payments')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/bills');

        });
    }

    /**
     * A test to check payment form visible
     *
     * @return void
     * @group hotel-payment
    */
    public function testHotelPaymentFormVisible()
    {
        $this->browse(function (Browser $browser) {
            $race  = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->waitFor('.list-group-item')
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->waitForText('Hotel Payments')
                ->assertSee('Hotel Payments')
                ->click('@hotel-payments')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/bills')
                ->assertSee('Payment Schedule');

        });
    }


    /**
     * A test to check payment form input validation
     *
     * @return void
     * @group hotel-payment
    */
    public function testHotelPaymentFormInput()
    {
        $this->browse(function (Browser $browser) {
            $race  = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->waitFor('.list-group-item')
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->waitForText('Hotel Payments')
                ->assertSee('Hotel Payments')
                ->click('@hotel-payments')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/bills')
                ->assertSee('Payment Schedule')
                ->waitFor('@'.$this->faker->numberBetween($min = 1, $max = 15))
                ->click('@'.$this->faker->numberBetween($min = 1, $max = 15))
                ->press('@add-bill')
                ->waitForText('Contract signed date is required')
                ->assertSee('Contract signed date is required')
                ->assertSee('Description is required')
                ->assertSee('Amount is required')
                ->assertSee('Due on required')
                ->type('@contract-signed-date', $this->faker->bs)
                ->type('@bill-amount-due', $this->faker->bs)
                ->type('@bill-due', $this->faker->bs)
                ->press('@add-bill')
                ->waitForText('The contract signed on is not a valid date')
                ->assertSee('The contract signed on is not a valid date')
                ->assertSee('Enter valid amount')
                ->assertSee('Enter valid date');

        });
    }

    /**
     * A test to check payment form input validation
     *
     * @return void
     * @group hotel-payment
    */
    public function testHotelPaymentFormInputAmtPaid()
    {
        $this->browse(function (Browser $browser) {
            $race  = $this->_createOneRace();
            $hotel = $this->_createOneHotel();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->waitFor('.list-group-item')
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->waitForText('Hotel Payments')
                ->assertSee('Hotel Payments')
                ->click('@hotel-payments')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/bills')
                ->assertSee('Payment Schedule')
                ->type('@amount-paid', $this->faker->bs)
                ->type('@amount-paid-on', $this->faker->bs)
                ->type('@payment-to-accounts-on', $this->faker->bs)
                ->type('@payment-invoice-date', $this->faker->bs)
                ->press('@add-bill')
                ->waitForText('Enter valid amount')
                ->assertSee('Enter valid amount')
                ->assertSee('Enter valid date')
                ->assertSeeIn('@payment-to-accounts-on-container', 'Enter valid date')
                ->assertSeeIn('@payment-invoice-date-container', 'Enter valid date');
        });
    }

    /**
     * A test to check payment form input validation
     *
     * @return void
     * @group hotel-payment
    */
    public function testHotelPaymentClearFormData()
    {
        $this->browse(function (Browser $browser) {
            $race    = $this->_createOneRace();
            $hotel   = $this->_createOneHotel();
            $amt_due = $this->faker->numberBetween($min = 1000, $max = 9000);
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->waitFor('.list-group-item')
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->waitForText('Hotel Payments')
                ->assertSee('Hotel Payments')
                ->click('@hotel-payments')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/bills')
                ->assertSee('Payment Schedule')
                ->type('@bill-amount-due', $amt_due)
                ->type('@amount-paid', $amt_due)
                ->type('@invoice-number', $amt_due)
                ->click('@clear-payment-schedule')
                ->click('@add-line')
                ->assertInputValueIsNot('@bill-amount-due', $amt_due)
                ->assertInputValueIsNot('@amount-paid', $amt_due)
                ->assertInputValueIsNot('@invoice-number', $amt_due);

        });
    }

    /**
     * A test to check payment save data
     *
     * @return void
     * @group hotel-payment
    */
    public function testHotelPaymentSaveFormData()
    {
        $this->browse(function (Browser $browser) {
            $race    = $this->_createOneRace();
            $hotel   = $this->_createOneHotel();
            $amt_due = $this->faker->numberBetween($min = 1000, $max = 9000);
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee('Hotel Inventory')
                ->clickLink('Add Hotel')
                ->assertSee('Search for a Hotel')
                ->type('@hotel-name-search', $hotel->name)
                ->waitFor('.list-group-item')
                ->clickLink($hotel->name)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->waitForText('Hotel Payments')
                ->assertSee('Hotel Payments')
                ->click('@hotel-payments')
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id.'/bills')
                ->assertSee('Payment Schedule')
                ->type('@contract-signed-date', date('d/m/Y'))
                ->waitFor('@' . $this->faker->numberBetween($min = 1, $max = 15))
                ->select('currency')
                ->select('exchange_currency')
                ->type('@bill-payment-name', $this->faker->bs)
                ->type('@bill-amount-due', $amt_due)
                ->type('@bill-due', date('d/m/Y'))
                ->type('@amount-paid', $amt_due)
                ->type('@amount-paid-on', date('d/m/Y'))
                ->type('@payment-to-accounts-on', date('d/m/Y'))
                ->type('@invoice-number', $amt_due)
                ->type('@payment-invoice-date', date('d/m/Y'))
                ->press('@add-bill')
                ->pause(2000)
                ->assertUrlIs(url()->current().'/races/'.$race->id.'/hotels/'.$hotel->id)
                ->assertSee('Payment Schedules have been saved.');

        });
    }
}
