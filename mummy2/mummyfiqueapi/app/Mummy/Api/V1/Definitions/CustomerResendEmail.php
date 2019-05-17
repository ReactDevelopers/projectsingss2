<?php
namespace App\Mummy\V1\Definitions;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="CustomerResendEmail"))
 */
class CustomerResendEmail
{
    /**
     * @SWG\Property(example="test@test.com")
     * @var string
     */
    public $email;
}