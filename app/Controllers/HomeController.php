<?php

namespace App\Controllers;

use App\Models\Order;
use App\Controllers\Controller;
use App\Services\PedidosService;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use App\Services\Orders\OrderService;

class HomeController extends Controller
{

    public function index(Request $request, Response $response, $args)
    {   

        $service = new OrderService();
        $totalsPrice = [
            'month' => $service->getTotalPrice('month',Order::STATUS_FINISH),
            'year' => $service->getTotalPrice('year',Order::STATUS_FINISH),
            'all' => $service->getTotalPrice(null,Order::STATUS_FINISH)
        ];
        $totalsQuant = [
            'month' => $service->getTotalQuant('month',Order::STATUS_FINISH),
            'year' => $service->getTotalQuant('year',Order::STATUS_FINISH),
            'all' => $service->getTotalQuant(null,Order::STATUS_FINISH)
        ];

        $view = '/pages/home/admin.twig';
        $params = [
            'totalsPrice' => $totalsPrice,
            'totalsQuant' => $totalsQuant
        ];
        return $this->view->render($response, $view, $params);
    }
    
    public function clearCache(Request $request, Response $response, $args)
    {
        \rrmdir("temp/");
        return \json([
            'message' => 'Cache removido com sucesso',
            'result' => 1,
            'action' => 'new'
        ]);
    }

}
