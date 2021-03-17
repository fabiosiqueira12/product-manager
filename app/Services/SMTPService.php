<?php

namespace App\Services;

use Exception;
use App\Services\Service;

class SMTPService extends Service 
{

    function __construct() {
        parent::__construct();
        $this->table = "smtp";
    }

    public function get()
    {
        return $this->genericLastInsert("a.*");
    }

    public function save($data)
    {
        return $this->genericeSave($data,['id'],false);
    }
}

?>