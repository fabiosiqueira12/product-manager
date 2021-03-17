<?php

namespace App\Services\Products;

use App\Services\Service;

class CategoryService extends Service{

    private $fields = "a.*";

    function __construct() {
        parent::__construct();
        $this->setTable("produto_categoria");
        $this->setPrefix("a");
    }

}

?>