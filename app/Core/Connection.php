<?php

namespace App\Core;

class Connection
{
    private $PDO;
    
    function __construct()
	{
        $DBConfig = require('app/db.config.php');
        $this->PDO = new \PDO(
        'mysql:host='. $DBConfig['host'].';port=3306;dbname='.$DBConfig['dbname'].';charset=utf8mb4',
        $DBConfig['user'],
        $DBConfig['pass']);
        $this->PDO->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    function returnConnection(){
        return $this->PDO;
    }
    
}

?>