<?php

namespace App\Controllers\Orders;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers\Controller;
use App\Models\Order;
use App\Services\ConsumerService;
use App\Services\Orders\OrderService;
use App\Services\Orders\ProductsService;
use App\Services\Products\ProductService;
use Exception;
use Slim\Exception\NotFoundException;
use Slim\Handlers\NotFound;

class IndexController extends Controller{

    private $folder = "pages/orders";
    private $urlPaginate = "pedidos/paginate";

    public function index(Request $request, Response $response, $args)
    {
        $service = new OrderService();
        $list = $service->paginate([
            'page' => 1,
            'forpage' => 20
        ]);

        $consumerService = new ConsumerService();
        $consumers = $consumerService->paginate([
            'page' => 1,
            'forpage' => 10000
        ])['results'];

        $totalsPrice = [
            'month' => $service->getTotalPrice('month',Order::STATUS_FINISH),
            'year' => $service->getTotalPrice('year',Order::STATUS_FINISH),
            'all' => $service->getTotalPrice(null,Order::STATUS_FINISH)
        ];

        return $this->view->render($response,"{$this->folder}/index.twig",[
            'list' => $list,
            'urlPaginate' => $this->urlPaginate,
            'list_type_payment' => \list_type_payment(),
            'list_status_order' => \list_status_order(),
            'consumers' => $consumers,
            'totalsPrice' => $totalsPrice
        ]);

    }

    public function search(Request $request, Response $response, $args)
    {

        $body = $request->getParsedBody();
        $page = isset($body['page']) && !empty($body['page']) ? \intval($body['page']) : 1;
        $body['page'] = $page;

        $service = new OrderService();
        $list = $service->paginate($body);

        return $this->view->render($response, 'helpers/_box-paginate.twig', [
            "folderList" => "{$this->folder}/_list.twig",
            "paramsList" => [
                'list' => $list['results']
            ],
            'list' => $list,
            "urlPaginate" => $this->urlPaginate
        ]);

    }

    public function paginate(Request $request, Response $response, $args)
    {

        $body = $request->getParsedBody();
        $page = isset($body['page']) && !empty($body['page']) ? \intval($body['page']) : 1;
        $body['page'] = $page;
        $service = new OrderService();
        $list = $service->paginate($body);

        return $this->view->render($response, \folder_paginate_box(), [
            "folderList" => "{$this->folder}/_list.twig",
            "paramsList" => [
                'list' => $list['results']
            ],
            'list' => $list,
            "urlPaginate" => $this->urlPaginate
        ]);
        
    }

    public function save(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!isset($body['id_consumer']) || empty($body['id_consumer'])){
            if (!isset($body['name']) || empty($body['name'])){
                return \throwJsonException("O nome do consumidor n??o foi informado");
            }
            if (isset($body['cpf']) && !\validate_cpf($body['cpf'])){
                return \throwJsonException("O CPF informado n??o ?? v??lido!");
            }
            if (isset($body['cnpj']) && !validate_cnpj($body['cnpj'])){
                return \throwJsonException("O CNPJ informado n??o ?? v??lido!");
            }
            $consumerService = new ConsumerService();
            $saveConsumer = $consumerService->save([
                'name' => $body['name'],
                'cpf' => isset($body['cpf']) ? $body['cpf'] : null,
                'cnpj' => isset($body['cnpj']) ? $body['cnpj'] : null,
                'email' => isset($body['email']) ? $body['email'] : null,
                'phone' => isset($body['phone']) ? $body['phone'] : null
            ],true);
            if (!is_int($saveConsumer)){
                return $saveConsumer;
            }
            $body['id_consumer'] = $saveConsumer;
        }

        $service = new OrderService();
        $code = create_codigo();
        $saveOrder = $service->save([
            'code' => $code,
            'type_payment' => $body['type_payment'],
            'id_consumer' => $body['id_consumer'],
            'id_user' => \user_logged()->getId()
        ],true);
        if (!is_int($saveOrder)){
            return $saveOrder;
        }
        return \json([
            'message' => 'Pedido criado com sucesso!',
            'result' => 1,
            'action' => 'goto',
            'link' => "pedidos/detalhes/{$code}"
        ]);

    }

    public function details(Request $request, Response $response, $args)
    {
        if (!isset($args['code']) || empty($args['code'])){
            throw new NotFoundException($request,$response);
        }
        $orderService = new OrderService();
        $order = $orderService->returnByParan("code",$args['code']);
        if (empty($order)){
            throw new NotFoundException($request,$response);
        }

        $productService = new ProductService();
        $products = $productService->paginate([
            'page' => 1,
            'forpage' => 10000,
            'id_order_confirm' => $order->getId()
        ])['results'];

        return $this->view->render($response,"{$this->folder}/details.twig",[
            'order' => $order,
            'products' => $products
        ]);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!isset($body['ref']) || empty($body['ref'])){
            throw new Exception("A refer??ncia do pedido n??o foi encontrada");
        }
        $service = new OrderService();
        $productService = new ProductsService();
        $cancelProducts = $productService->cancelAllProducts($body['ref']);
        $delete = $service->delete($body['ref']);

        return \json(['message' => 'Removido com sucesso','result' => 1,'action' => 'search']);

    }

    public function finish(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!isset($body['ref']) || empty($body['ref'])){
            throw new Exception("A refer??ncia do pedido n??o foi encontrada");
        }
        $service = new OrderService();
        $order = $service->returnByParan("id",$body['ref']);
        if (empty($order)){
            throw new Exception("O pedido n??o foi encontrado");
        }
        if (empty($order->getQtdProducts())){
            throw new Exception("O pedido n??o tem produtos para finalizar");
        }
        $finish = $service->updateStatus(Order::STATUS_FINISH,$body['ref']);

        $action = isset($body['action']) && !empty($body['action']) ? $body['action'] : 'new';
        return \json(['message' => 'Finalizado com sucesso','result' => 1,'action' => $action]);

    } 

    public function cancel(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!isset($body['ref']) || empty($body['ref'])){
            throw new Exception("A refer??ncia do pedido n??o foi encontrada");
        }
        $service = new OrderService();
        $cancel = $service->updateStatus(Order::STATUS_BLOCK,$body['ref']);
        $action = isset($body['action']) && !empty($body['action']) ? $body['action'] : 'new';
        return \json(['message' => 'Finalizado com sucesso','result' => 1,'action' => $action]);

    } 

}

?>