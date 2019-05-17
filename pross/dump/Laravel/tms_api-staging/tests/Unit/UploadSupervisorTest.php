<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Lib\DataVerify\SupervisorDataVerify;
use Illuminate\Support\Collection;

class UploadSupervisorTest extends ColumnTestCase
{
    use DatabaseTransactions;

    protected $row = [];
    protected $dataVerifyClass = SupervisorDataVerify::class;
    protected $per_id;
    protected $sup_id;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_when_empty_data()
    {
        $this->getToken();
        $this->actingAs($this->authUser);

        $placement_data_ins = new SupervisorDataVerify([],[]);
        $result = $placement_data_ins->run();
        $this->assertTrue(!$result['status']);
    }
    
    /**
     * Verify the Per_id column value
     */

    public function test_per_id() {

        $this->setColumn('per_id');

        # When  is blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When Contain Real Text 
        $this->createNewData()->makeSomeChar(20)->checkColumn(false);

        # When special chars
        $this->createNewData()->makeSpecialChar(30)->checkColumn(false);

        
        # when per_id does not exist in database.        
        $this->createNewData();
        $this->per_id = $this->row['per_id']+1;
        $this->checkColumn(false);
     }

     /**
     * Verify the sup_id column value
     */

    public function test_sup_id() {

        $this->setColumn('sup_id');

        # When  is blank
        $this->createNewData()->makeBlank()->checkColumn(true);

        # When Contain Real Text 
        $this->createNewData()->makeSomeChar(20)->checkColumn(false);

        # When special chars
        $this->createNewData()->makeSpecialChar(30)->checkColumn(false);

        
        # when sup_id does not exist in database.        
        $this->createNewData();
        $this->sup_id = $this->row['sup_id']+1;
        $this->checkColumn(false);
     }

    /**
     * Create new Data
     */
    protected function createNewData() {

        $this->getToken();
        $this->actingAs($this->authUser);
        $this->row = $this->getSupervisorData();        
        return $this;
    }
    protected function ins() {        

        $per_id  = $this->per_id ? $this->per_id : $this->row['per_id'];
        $sup_id  = $this->sup_id ? $this->sup_id : $this->row['sup_id'];
        return  new $this->dataVerifyClass($this->row, [$sup_id, $per_id]);
    }

    /**
     * Create the fake data of Course Run
     */
    protected function getSupervisorData() {
        
        $faker = \Faker\Factory::create();        
       $per_id = $faker->numberBetween(6754, 10000);
       $sup_id = $per_id +1;
        return [
            'sup_id'=> $sup_id,
            'per_id'=> $per_id,
        ];
    }

}