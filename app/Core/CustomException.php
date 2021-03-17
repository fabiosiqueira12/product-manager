<?php

namespace App\Core;

use Exception;

class CustomException extends Exception
{
    public function __construct($message = null, $code = 0){
        parent::__construct();
        $this->message = $message;
        $this->code = $code;
    }
}