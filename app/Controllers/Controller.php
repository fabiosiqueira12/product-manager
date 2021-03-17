<?php

namespace App\Controllers;

use Slim\Container;

abstract class Controller
{
    protected $ci;

    /**
     * Controller constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->ci = $container;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->ci->has($name)) {
            return $this->ci->get($name);
        }
    }

}


?>