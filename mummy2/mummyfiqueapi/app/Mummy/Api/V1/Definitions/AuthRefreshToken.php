<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 3/13/17
 * Time: 16:19
 */

namespace App\Mummy\V1\Definitions;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name=""))
 */
class AuthRefreshToken
{
    /**
     * @SWG\Property(example="refresh_token")
     * @var string
     */
    public $grant_type;
    /**
     * @SWG\Property(example="CR/y3sHfRoMtYEf6Qw1s/o0zWKPKdPC51mqgzN0eQSNIyszZZtXVPlGbJLP7mL0EcQH9eHVbFRXbkcvAgAjkFMBCTYDwHELUINV5kP5sNbKBrwNPC1aFYM1kIY3EoTLiJqBG0438SwITXvgQBeuicLS1rIHzGFmLhCT1ZZXcZQ0u/UZ2xMU/pDA3l78LmjLCYdW2JjUw0lVcZvRgGf3eNU/Pi4pdDe+KHp4OIxb0pwYtpW6ZgNuERAHBZTpYIcoVzpq867ejUqevfV1vp9hr3YAgHFhwZjxl1H+Pm/DOXUoJPS9BZB1uxChzRxpSmXEsQDoQJANUQX8RkgAXPKnuCzU0/fmd9JluDlcN0TEKSthkYTirMjrdHbN1J2kfsa4zE7gZCYPf1KL+WSRnynnFmgEKnD8BnniCnBSSulA6IS+Mt8XsTqW1qlyn8WmedoSVMEUVMZRKBS3w5Vcat7+1ivnFOJDwFbqlIEwv6mQJCjbwxEKZggI9aZnd06PfDyuAfwvzfbx2Qt7ttj0182PJbgp8fNSM+JrlWQZb+3D7jy2H+o5cgyJnxMr70qyUtzNnKL1pPhGOpg+rpY6aFUdZ4FkqLmc4y13Gb0fnC3j8Az0gNfzj0DuNRLkuuY9l6D1KsT6VCziSM4ZkFGAuxRxeQ+tePul8sb41G37Y6+72wOg=")
     * @var string
     */
    public $refresh_token;
    /**
     * @SWG\Property(example="2", format="int32" )
     * @var int
     */
    public $client_id;
    /**
     * @SWG\Property(example="NgTPQpcuEAWsy31v6uiAAxwU5UFiJULO8yg5oIA7")
     * @var string
     */
    public $client_secret;
}