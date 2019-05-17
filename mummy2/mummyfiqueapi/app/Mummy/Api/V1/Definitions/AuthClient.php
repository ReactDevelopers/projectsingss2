<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 3/13/17
 * Time: 16:19
 */

namespace App\Mummy\V1\Definitions;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="refreshToken"))
 */
class AuthClient
{
    /**
     * @SWG\Property(example="lvonrueden@example.net")
     * @var string
     */
    public $username;
    /**
     * @SWG\Property(example="secret")
     * @var string
     */
    public $password;
    /**
     * @SWG\Property(example="password")
     * @var string
     */
    public $grant_type;
    /**
     * @SWG\Property(example="2", format="int32" )
     * @var int
     */
    public $client_id;
    /**
     * @SWG\Property(example="G2yJmIjtCfQJPCq752D0z3Csc9QC6hwsdT2XuaiK")
     * @var string
     */
    public $client_secret;
}