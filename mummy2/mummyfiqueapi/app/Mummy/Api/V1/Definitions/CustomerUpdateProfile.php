<?php
namespace App\Mummy\V1\Definitions;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="UserUpdateProfile"))
 */
class CustomerUpdateProfile
{
    /**
     * @SWG\Property(format="int64",example="7c4a8d09ca3762af61e59520943dc26494f8941b")
     * @var string
     */
    public $token;
    /**
     * @SWG\Property(example="name")
     * @var string
     */
    public $name;
    /**
     * @SWG\Property(example="AppsCyclone")
     * @var string
     */
    public $company;

    /**
     * @SWG\Property(example="12345678")
     * @var string
     */
    public $phone;
    /**
     * @SWG\Property(example="1483604126")
     * @var integer
     */
    public $dob;

}