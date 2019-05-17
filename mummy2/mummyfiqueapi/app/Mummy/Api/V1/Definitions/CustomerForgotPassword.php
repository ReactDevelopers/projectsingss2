<?php
namespace App\Mummy\V1\Definitions;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="CustomerForgotPassword"))
 */
class CustomerForgotPassword
{
    /**
     * @SWG\Property(example="test@test.com")
     * @var string
     */
    public $email;
}