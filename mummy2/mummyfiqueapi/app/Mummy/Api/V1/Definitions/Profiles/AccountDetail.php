<?php
namespace App\Mummy\V1\Definitions\Profiles;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class AccountDetail
{
	/**
     * @SWG\Property(example="123456789")
     * @var string
     */
    public $first_name;
    /**
     * @SWG\Property(example="123456789")
     * @var string
     */
    public $last_name;
    /**
     * @SWG\Property(example="123456789")
     * @var string
     */
    public $phone_code;
    /**
     * @SWG\Property(example="123456789")
     * @var string
     */
    public $phone;

   /**
     * @SWG\Property(example="123456789")
     * @var string
     */
    public $facebook_id;
    
}