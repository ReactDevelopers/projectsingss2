<?php

namespace App\Exceptions;

use RuntimeException;

class FileHeaderNotMatchException extends RuntimeException
{
    private $requiredHeader =[];

    public function __construct(Array $required_header)
    {
        parent::__construct('The given file was invalid.');
        $this->requiredHeader = $required_header;
    }   

    public function getResponse()
    {
        return $this->requiredHeader;
    }
}
