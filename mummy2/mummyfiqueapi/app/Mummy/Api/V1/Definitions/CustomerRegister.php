<?php
namespace App\Mummy\V1\Definitions;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="CustomerRegister"))
 */
class CustomerRegister
{
    /**
     * @SWG\Property(example="test@test.com")
     * @var string
     */
    public $email;
    /**
     * @SWG\Property(format="int64",example="7c4a8d09ca3762af61e59520943dc26494f8941b")
     * @var string
     */
    public $password;
    /**
     * @SWG\Property(example="name")
     * @var string
     */
    public $name;
}