<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Lib\DataVerify\CourseDataVerify;

class UploadCourseTest extends ColumnTestCase
{
    protected $row = [];
    
    protected $dataVerifyClass = \App\Lib\DataVerify\CourseDataVerify::class;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_when_empty_data()
    {
        $this->getToken();
        $this->actingAs($this->authUser);

       $course_data_ins = new CourseDataVerify([]);
       $result = $course_data_ins->run();
       $this->assertTrue(!$result['status']);
    }

    /**
     * Verify the course code cell data
     */
    public function test_course_code() {

        $this->getToken();
        $this->actingAs($this->authUser);       

        $this->setColumn('course_code');

        # When Course Code is blank
        $this->createNewData()->makeBlank()->checkColumn(false);
        # When course Code contains  more than 300 characters         
        $this->createNewData()->makeSomeChar(21)->checkColumn(false);
        # When course Code contains special chars
        $this->createNewData()->makeSpecialChar(20)->checkColumn(false);

        # When course Code conatins only digits
        $this->createNewData()->makeDigit(17)->checkColumn(true);

        # When course Code conatins only Alpha
        $this->createNewData()->makeAlpha(18)->checkColumn(true);

        # When Valid data
        $this->createNewData()->checkColumn(true);
    }

    /**
     * Verify the Course Title value
     */

    public function test_course_title() {
        
        $this->getToken();
        $this->actingAs($this->authUser);
        //$faker = \Faker\Factory::create();

        $this->setColumn('course_title');

        # When Course title is blank
        $this->createNewData()->makeBlank()->checkColumn(false);
        # When course title contains  more than 300 characters         
        $this->createNewData()->makeSomeChar(300)->checkColumn(false);
        # When course title contains special chars
        $this->createNewData()->makeSpecialChar(20)->checkColumn(true);

        # When course title conatains valid data
        $this->createNewData()->makeSomeChar(255)->checkColumn(true);

        # When Valid data
        $this->createNewData()->checkColumn(true);

    }

    public function test_duration_no_of_days() {

        $this->getToken();
        $this->actingAs($this->authUser);
        //$faker = \Faker\Factory::create();

        $this->setColumn('duration_no_of_days');

         # When  blank
         $this->createNewData()->makeBlank()->checkColumn(false);

         # When Contain Real Text 
         $this->createNewData()->makeSomeChar(20)->checkColumn(false);

         # When Contain DIGITS
         $this->createNewData()->makeDigit(10, 999)->checkColumn(true);

         # When Contain number greater than 999
         $this->createNewData()->makeDigit(1000, 9999)->checkColumn(false);

         # When special chars
         $this->createNewData()->makeSpecialChar(30)->checkColumn(false);

         # When valid data
         $this->createNewData()->checkColumn(true);
    }

    public function test_programme_category() {

        $this->getToken();
        $this->actingAs($this->authUser);
        //$faker = \Faker\Factory::create();

        $this->setColumn('programme_category');

        # When  blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When  random text
        $this->createNewData()->makeSomeChar(200)->checkColumn(false);

        # When  contains special chars
        $this->createNewData()->makeSpecialChar(20)->checkColumn(false);

        # When contain the valid category name
        $this->createNewData()->checkColumn(true);

    }

    public function test_programme_type() {
        
        $this->getToken();
        $this->actingAs($this->authUser);

        $this->setColumn('programme_type');

        # When  blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When  random text
        $this->createNewData()->makeSomeChar(200)->checkColumn(false);

        # When  contains special chars
        $this->createNewData()->makeSpecialChar(20)->checkColumn(false);

        # When contain the valid category name
        $this->createNewData()->checkColumn(true);
    }

    
    public function test_mandatory_yn() {

        $this->getToken();
        $this->actingAs($this->authUser);

        $this->setColumn('mandatory_yn');

        # When  blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When  random text
        $this->createNewData()->makeSomeChar(200)->checkColumn(false);

        # When  value is yes but y is in small letter
        $this->createNewData()->setValue('yes')->checkColumn(true)->checkTraslateValue('Yes','mandatory');

        # When  value is no but n is in small letter
        $this->createNewData()->setValue('no')->checkColumn(true)->checkTraslateValue('No','mandatory');

        # When  value is caps
        $this->createNewData()->setValue('YES')->checkColumn(true)->checkTraslateValue('Yes','mandatory');

        # When  value is caps
        $this->createNewData()->setValue('NO')->checkColumn(true)->checkTraslateValue('No', 'mandatory');

        # When  valid data
        $this->createNewData()->setValue('Yes')->checkColumn(true);

        # When  valid data
        $this->createNewData()->setValue('No')->checkColumn(true);

        # When  valid data
        //$this->createNewData()->setValue('yes by law')->checkColumn(true)->checkTraslateValue('Yes by law','mandatory');
    }

    public function test_delivery_method() {
        
        $this->getToken();
        $this->actingAs($this->authUser);
        //$faker = \Faker\Factory::create();

        $this->setColumn('delivery_method');

        # When delivery_method is blank
        $this->createNewData()->makeBlank()->checkColumn(true);
        # When delivery_method contains  more than 300 characters         
        //$this->createNewData()->makeSomeChar(300)->checkColumn(false);
        # When delivery_method contains special chars
        //$this->createNewData()->makeSpecialChar(20)->checkColumn(true);

        # When delivery_method conatins valid data
        $this->createNewData()->makeSomeChar(255)->checkColumn(false);

        # When Valid data
        //$this->createNewData()->checkColumn(true);

    }

    public function test_training_location() {
        
        $this->getToken();
        $this->actingAs($this->authUser);

        $this->setColumn('training_location');

        # When  blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When  random text
        $this->createNewData()->makeSomeChar(200)->checkColumn(false);

        # When  contains special chars
        $this->createNewData()->makeSpecialChar(20)->checkColumn(false);

        # When contain the valid category name
        $this->createNewData()->checkColumn(true);
    }

    public function test_course_provider() {
        
        $this->getToken();
        $this->actingAs($this->authUser);

        $this->setColumn('course_provider');

        # When  blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When contains  more than 300 characters         
        $this->createNewData()->makeSomeChar(300)->checkColumn(false);
        # When contains special chars
        $this->createNewData()->makeSpecialChar(20)->checkColumn(true);

        # When conatins valid data
        $this->createNewData()->makeSomeChar(255)->checkColumn(true);

        # When contain the valid
        $this->createNewData()->checkColumn(true);
    }

    /**
     * @group test_cost_per_pax
     */
    public function test_cost_per_pax() {

        $this->getToken();
        $this->actingAs($this->authUser);
        //$faker = \Faker\Factory::create();

        $this->setColumn('costpax_without_gst');

         # When  blank
         $this->createNewData()->makeBlank()->checkColumn(true);

         # When Contain Real Text 
         $this->createNewData()->makeSomeChar(20)->checkColumn(false);

         # When Contain DIGITS
         $this->createNewData()->makeDigit(10, 999)->checkColumn(true);

         # When Contain number greater than 99999999
         $this->createNewData()->makeDigit(1000000, 99999999)->checkColumn(false);

         # When special chars
         $this->createNewData()->makeSpecialChar(30)->checkColumn(false);

         # When valid data
         $this->createNewData()->checkColumn(true);
    }

    public function test_grantsubsidy_yn() {

        $this->getToken();
        $this->actingAs($this->authUser);
        //$faker = \Faker\Factory::create();

        $this->setColumn('grant');

       # When  blank
        $this->createNewData()->makeBlank()->checkColumn(true);

        # When  random text
        $this->createNewData()->makeSomeChar(200)->checkColumn(false);

        # When  value is yes but y is in small letter
        $this->createNewData()->setValue('yes')->checkColumn(true)->checkTraslateValue('Yes','type_of_grant');

        # When  value is no but n is in small letter
        $this->createNewData()->setValue('no')->checkColumn(true)->checkTraslateValue('No','type_of_grant');

        # When  value is caps
        $this->createNewData()->setValue('YES')->checkColumn(true)->checkTraslateValue('Yes','type_of_grant');

        # When  value is caps
        $this->createNewData()->setValue('NO')->checkColumn(true)->checkTraslateValue('No', 'type_of_grant');

        # When  valid data
        $this->createNewData()->setValue('Yes')->checkColumn(true);

        # When  valid data
        $this->createNewData()->setValue('No')->checkColumn(true);
       
    }

    // public function test_if_yes_provide_value() {

    //     $this->getToken();
    //     $this->actingAs($this->authUser);

    //     $this->setColumn('if_yes_provide_value');

    //     # Subsidy not granted and subsidy value is presnt in column 'if_yes_provide_value'

    //     $this->createNewData()
    //         ->setvalue('No','grantsubsidy_yn')
    //         ->setValue(10)
    //         ->setvalue(20,'cost_per_pax')
    //         ->checkColumn(false);    
    // }

    public function test_vendor_contactemail_account() {
        
        $this->getToken();
        $this->actingAs($this->authUser);
        //$faker = \Faker\Factory::create();

        $this->setColumn('funding_type');

        # When Course title is blank
        $this->createNewData()->makeBlank()->checkColumn(true);
        # When course title contains  more than 300 characters         
        $this->createNewData()->makeSomeChar(300)->checkColumn(false);
        # When course title contains special chars
        $this->createNewData()->makeSpecialChar(20)->checkColumn(true);

        # When course title conatins valid data
        $this->createNewData()->makeSomeChar(255)->checkColumn(true);

        # When Valid data
        $this->createNewData()->checkColumn(true);

    }

    protected function createNewData() {

        $this->getToken();
        $this->actingAs($this->authUser);
        $this->row = $this->getCourseData(); 
        return $this;
    }
    protected function getCourseData() {
        
        $faker = \Faker\Factory::create();
        $grantsubsidy_yn = $faker->randomElement(['Yes','No']);
        $if_yes_provide_value = null;
        $cost_per_pax = $faker->randomFloat(2, 100,1000);
        if($grantsubsidy_yn === 'Yes') {
            
            $min = round($cost_per_pax - 50);
            $max = $cost_per_pax;
            $if_yes_provide_value = $faker->randomFloat(2, $min, $max);
        }

        return [
            'course_code' => $faker->unique()->regexify('[A-Z]{4}[0-9]{2,8}'),
            'course_title' => $faker->text(255),
            'duration_no_of_days'=>$faker->numberBetween(2,30),
            'programme_category'=> (App\Models\ProgrammeCategory::inRandomOrder()->first())->prog_category_name,
            'programme_type' => (App\Models\ProgrammeType::inRandomOrder()->first())->prog_type_name,
            'mandatory_yn' => $faker->randomElement(['Yes','Yes by law','No']),
            'delivery_method' => $faker->text(255),
            'training_location'=>(App\Models\TrainingLocation::inRandomOrder()->first())->location,
            'course_provider' => $faker->text(40),
            'cost_per_pax' => $cost_per_pax,
            'grantsubsidy_yn'=>$grantsubsidy_yn,
            'if_yes_provide_value' => $if_yes_provide_value,
            'vendor_contactemail_account' => $faker->text(255)
        ];
    }
}