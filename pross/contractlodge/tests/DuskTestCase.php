<?php

namespace Tests;

use App\Race;
use App\User;
use App\Hotel;
use App\Client;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * The user we will use to login and do stuff
     * @var App\User
     */
    public $user;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
    */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
    */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--headless',
            '--disable-gpu',
            '--window-size=1920,1080',
            '--disable-extensions',
            '--disable-translate',
            '--start-maximized',
            '--no-proxy-server',
            '--no-sandbox',
            '--verbose',
            // '--enable-features=NetworkService',
            // '--log-path=' . storage_path("logs/chromedriver-errors.log"),
            // '--proxy-server="direct://"',
            // '--proxy-bypass-list=*',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Setup the database for the tests
    */
    public function setUp()
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        // $this->withoutMiddleware();
        $this->artisan('migrate:fresh');
        $this->artisan('db:seed');
        $this->artisan('config:clear');

        $this->user = $this->_createOneUser();
    }

    /**
     * Tear down the testing suite
    */
    public function tearDown()
    {
        session()->flush();

        foreach (static::$browsers as $b) {
            $b->driver->manage()->deleteAllCookies();
        }

        $this->artisan('config:clear');

        parent::tearDown();
    }

    /**
     * Create a user
    */
    public function _createOneUser()
    {
        return factory(User::class)->create();
    }

    /**
     * Create a race
    */
    public function _createOneRace()
    {
        return factory(Race::class)->create();
    }

    /**
     * Create a hotel
    */
    public function _createOneHotel()
    {
        return factory(Hotel::class)->create();
    }

    /**
     * Create a client
    */
    public function _createOneClient()
    {
        return factory(Client::class)->create();
    }
}
