<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Services\PedidosService;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class HomeController extends Controller
{

    public function index(Request $request, Response $response, $args)
    {   

        $view = '/pages/home/admin.twig';
        $params = [
        ];
        return $this->view->render($response, $view, $params);
    }
    

}