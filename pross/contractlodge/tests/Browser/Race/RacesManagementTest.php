<?php

namespace Tests\Browser\Race;
use App\Race;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
class RacesManagementTest extends DuskTestCase
{
    use WithFaker;
    public $race;
    /**
     * A test to check if a race can be created successfully with correct fields
     *
     * @group  race
     * @return void
    */
    public function testRaceCanBeCreated()
    {
        $this->browse(function (Browser $browser) {
        $date = $this->faker->date('d/m/Y');
        $name = $this->faker->bs;
        $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
        $browser->loginAs($this->user)
            ->visit('/home')
            ->assertSee('Active Races')
            ->press('@add-race')
            ->assertPathIs('/races/create')
            ->type('race_code', $race_code)
            // FIXME: 2037 is a mysql limitation for date fields (!!!)
            ->type('year', $this->faker->numberBetween(2019, 2037))
            ->type('name', $name)
            // FIXME: This isn't awesome. But there's an issue in vue-date-pick.
            // This should be type() or keys(), not value().
            ->value('input[name=start_on]', $this->faker->dateTimeBetween('+1 week', '+1 month')->format('d/m/Y'))
            ->value('input[name=end_on]', $this->faker->dateTimeBetween('+2 week', '+3 month')->format('d/m/Y'))
            ->select('currency_id', $this->faker->numberBetween(1, 3))
            ->press('@race-submit')
            ->waitFor('.alert-success')
            ->assertSee($name)
            ->assertPresent('.alert-success');
        });
    }
    /**
     * A test to check if a race can be archived.
     *
     * @group  race
     * @return void
    */
    public function testRaceCanBeArchived()
    {
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@race-archive')
                ->acceptDialog()
                ->waitFor('.alert-success')
                ->assertPresent('.alert-success')
                ->assertDontSee($race->year.' '.$race->name);
        });
    }
    /**
     * A test to check if a race can be unarchived
     *
     * @group  race
     * @return void
    */
    public function testRaceCanBeUnarchived()
    {
        //$this->markTestSkipped('Temporary skip.');
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@race-archive')
                ->acceptDialog()
                ->assertPresent('.alert-success')
                ->assertDontSee($race->year.' '.$race->name)
                ->visit('/races/archived')
                ->assertSee("Archived Races")
                ->press('@race-unarchive')
                ->acceptDialog()
                ->waitFor('.alert-success')
                ->assertPresent('.alert-success')
                ->assertDontSee($race->year.' '.$race->name);
        });
    }
    /**
     * A test to check if a race can be created successfully with correct fields
     *
     * @group  race
     * @return void
    */
    public function testRaceCanBeEdited(){
        $this->browse(function (Browser $browser) {
            $race = $this->_createOneRace();
            $year = $race->year;
            $oldYear = $year - 1;
            $name = $race->name;
            $editedName = $name . ' edited';
            $start_on = $race->start_on;
            $end_on = $race->end_on;
            $race_code = $this->faker->word.'-'.$this->faker->numberBetween(10,9999);
            $browser->loginAs($this->user)
                ->visit('/races/' .$race->id)
                ->pause(5000)
                ->click('@race-edit')
                ->assertSee('Edit Race')
                ->type('@race-code', $race_code)
                ->type('year', $oldYear)
                ->type('name', $editedName)
                ->select('currency_id', $this->faker->numberBetween(2, 3))
                ->press('@race-edit-submit')
                ->waitFor('.alert-success')
                ->assertPresent('.alert-success')
                ->assertSee($oldYear)
                ->assertSee($editedName)
                ->assertDontSee($start_on)
                ->assertDontSee($end_on);
        });
    }

    /**
     * A test to check archived races
     *
     * @group  race
     * @return void
    */
    public function testViewArchivedRaces()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/home')
                ->assertSee('Active Races')
                ->press('@view-race-archive')
                ->waitForText('Archived Races')
                ->assertSee('Archived Races');
        });
    }
}
