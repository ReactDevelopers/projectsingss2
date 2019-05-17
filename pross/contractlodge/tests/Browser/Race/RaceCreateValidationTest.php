<?php

namespace Tests\Browser;
use App\User;
use App\Race;
use App\Hotel;
use App\Client;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RaceCreateValidationTest extends DuskTestCase
{

    use WithFaker;

    /**
     * The user we will get to create the race
     * @var App\Race
    */
    public $race;

    /**
     * Setup the race and user for the test
    */
    public function setUp()
    {
        parent::setUp();
        $this->race = $this->_createOneRace();
    }

    /**
     * A Test to check validation
     *
     * @group  race
     * @return void
     */
    public function testValidateRace()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertSee('Create New Race')
                ->press('@race-submit')
                ->assertSee('Race code is required')
                ->assertSee('Year is required')
                ->assertSee('Race name is required');
        });
    }

    /**
     * A Test to check validation
     *
     * @group  race
     * @return void
     */
    public function testRaceInputTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertPathIs('/races/create')
                ->type('race_code', $this->faker->numberBetween($min = 1000, $max = 9000))
                ->keys('input[name=name]', $this->faker->numberBetween($min = 1000, $max = 9000))
                ->press('@race-submit')
                ->assertPathIs('/races/create')
                ->assertSee('Year is required');
        });
    }

    /**
     * A Test to check validation
     *
     * @group  race
     * @return void
     */
    public function testRaceCancelTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/races/create')
                ->assertSee('Create New Race')
                ->press('@race-cancel')
                ->assertSee('Active Races');
        });
    }


    /**
     * A Test to check validation
     *
     * @group  race
     * @return void
     */
    public function testOpenCreateRace()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
            ->visit('/home')
            ->assertSee('Active Races')
            ->press('@add-race')
            ->assertSee('Create New Race');
        });
    }

    /**
     * A Test to check validation
     *
     * @group  race
     * @return void
     */
    public function testValidateInputRace()
    {
        $this->browse(function (Browser $browser) {
            $name = $this->faker->bs;
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@add-race')
                ->assertSee('Create New Race')
                ->type('input[name=year]', $this->faker->year($max = 'now'))
                ->value('input[name=start_on]', $this->faker->date($format = 'd/m/Y', $max = 'now'))
                ->value('input[name=end_on]', $this->faker->date($format = 'd/m/Y', $max = '+1 years'))
                ->keys('input[name=name]', $this->faker->numberBetween($min = 1000, $max = 9000))
                ->press('@race-submit')
                ->assertSee('Year should not be less than current year')
                ->assertSee('Race name should be string');
        });
    }

    /**
     * A test to check race open
     *
     * @group  race
     * @return void
    */
    public function testRaceOpen(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->year.' '.$race->name)
                ->press('@race-edit')
                ->assertSee('Edit Race');
        });
    }

    /**
     * A test to check race code format
     *
     * @group  race
     * @return void
    */
    public function testRaceCode(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@race-edit')
                ->assertSee('Edit Race')
                ->type('input[name=race_code]' , '//&&!!')
                ->press('@race-edit-submit')
                ->assertSee('Race code should be like "Race-1" format');
        });
    }

    /**
     * A test to check race code format
     *
     * @group  race
     * @return void
    */
    public function testRaceCodeBlank(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@race-edit')
                ->assertSee('Edit Race')
                ->type('input[name=race_code]' , null)
                ->press('@race-edit-submit')
                ->assertSee("Race code is required");
        });
    }


    /**
     * A test to check race code format
     *
     * @group  race
     * @return void
    */
    public function testRaceDateCheck(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@race-edit')
                ->assertSee('Edit Race')
                ->value('input[name=start_on]' , $this->faker->date($format = 'd/m/Y', $max = 'now'))
                ->press('@race-edit-submit');
        });
    }

    /**
     * A test to check race code format
     *
     * @group  race
     * @return void
    */
    public function testRaceCodeString(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@race-edit')
                ->assertSee('Edit Race')
                ->type('@race-code' , $this->faker->bs)
                ->press('@race-edit-submit')
                ->assertDontSee("Please enter valid race code format");
        });
    }


    /**
     * A test to check race date format
     *
     * @group  race
     * @return void
    */
    public function testRaceDateTest(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@race-edit')
                ->assertSee('Edit Race')
                ->value('input[name=start_on]' , $this->faker->date($format = 'd/m/Y', $max = 'now'))
                ->value('input[name=end_on]' , $this->faker->date($format = 'd/m/Y', $max = 'now'))
                ->press('@race-edit-submit')
                ->assertDontSee('.invalid-feedback');
        });
    }


    /**
     * A test to check race verify currency
     *
     * @group  race
     * @return void
    */
    public function testRaceVerifyCurrency(){
        $this->browse(function (Browser $browser) {
            $currency = $this->faker->numberBetween($min = 1, $max = 5);
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@race-edit')
                ->assertSee('Edit Race')
                ->select('currency_id', $currency)
                ->assertSelected('currency_id', $currency);
        });
    }

    /**
     * A test to check race code after save verify url
     *
     * @group  race
     * @return void
    */
    public function testRaceVerifyRedirect(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@race-edit')
                ->assertSee('Edit Race')
                ->type('input[name=race_code]', $this->faker->word.'-'.$this->faker->numberBetween($min = 1000, $max = 9000))
                ->type('input[name=year]', \Carbon\Carbon::now()->year)
                ->type('input[name=name]', $this->faker->bs)
                ->value('input[name=start_on]', date('d/m/Y'))
                ->value('input[name=end_on]', date('d/m/Y', strtotime('+1 months')))
                ->select('currency_id', $this->faker->randomDigit)
                ->press('@race-edit-submit')
                ->assertSee('The race has been updated.')
                ->assertUrlIs(url()->current().'/races/'.$race->id);
        });
    }


    /**
     * A test to check race code after cancel verify url
     *
     * @group  race
     * @return void
    */
    public function testRaceVerifyCancelRedirect(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/races/'.$race->id)
                ->assertSee($race->name)
                ->press('@race-edit')
                ->assertSee('Edit Race')
                ->press('@race-edit-cancel')
                ->assertUrlIs(url()->current().'/races/'.$race->id);
        });
    }

}
