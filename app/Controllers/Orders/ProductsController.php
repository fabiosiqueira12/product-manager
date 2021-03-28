<?php

namespace App\Controllers\Orders;

use App\Models\Order;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers\Controller;
use App\Services\Orders\OrderService;
use App\Services\Orders\ProductsService;
use App\Services\Products\ProductService;
use App\Services\Orders\ProductsService AS ProductsOrder;

class ProductsController extends Controller
{
    
    private $folder = "pages/orders/products";

    public function delete(Request $request, Response $response, $args)
    {

        $body = $request->getParsedBody();
        if (!isset($body['id_order']) || $body['id_order'] == ""){
            return \throwJsonException("A referência do pedido não foi encontrada");
        }
        if (!isset($body['ref']) || $body['ref'] == ""){
            return \throwJsonException("A referência do produto");
        }

        $service = new ProductsService();
        
        $delete = $service->remove(
            $body['id_order'],
            $body['ref']
        );

        return \json([
            'message' => 'Removido com sucesso',
            'result' => 1
        ]);
    }

    public function updateEstoque(Request $request, Response $response, $args)
    {

        $body = $request->getParsedBody();
        $qtdeAtual = $body['qtde_atual'];
        
        $difference = $body['quantidade'] - $qtdeAtual;
        if ($difference > 0){
            if ($difference > $body['estoque']){
                return \throwJsonException("Você não pode adicionar mais do que o estoque atual");
            }
        }

        $service = new ProductsService();
        $updateQuant = $service->updateQuant(
            $difference,
            $body['id_product'],
            $body['id_order']
        );

        return \json([
            'message' => 'Atualizado com sucesso',
            'result' => 1,
            'action' => 'reload'
        ]);

    }

    public function search(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $service = new ProductService();
        $list = $service->paginate([
            'search' => $body['search_add'],
            'page' => 1,
            'forpage' => 200,
            'id_order' => $body['id_order']
        ]);
        return $this->view->render($response,"{$this->folder}/_list.twig",[
            'list' => $list['results']
        ]);
    }

    public function addMany(Request $request, Response $response, $args)
    {

        $body = $request->getParsedBody();
        
        if (!isset($body['produtos']) || count($body['produtos']) == 0){
            return \throwJsonException("Você precisa selecionar os produtos");
        }
        
        if (!isset($body['id_order']) || empty($body['id_order'])){
            return \throwJsonException("A referência do pedido não foi encontrada");
        }

        $orderService = new OrderService();
        $order = $orderService->returnByParan("id",$body['id_order']);
        if (empty($order)){
            return \throwJsonException("O pedido não foi encontrado");
        }

        $service = new ProductsOrder();

        $produtos = $body['produtos'];
        $addProdutos = [];
        
        foreach($produtos as $v){
            $produto = $v['product'];  
            $valorFinal = $order->getTypePayment() == Order::TYPE_MONEY && !empty($produto['price_money']) ? $produto['price_money'] : $produto['price_seller'];
            if ($valorFinal != null && $valorFinal != 0){
                $addProdutos[] = [
                    'id_product' => $produto['id'],
                    'quant' => $v['quant'],
                    'price' => $valorFinal
                ];
            }
        }

        $addSave = $service->saveMany(
            $addProdutos,
            $order->getId()
        );
        
        return \json([
            'message' => 'Adicionado com sucesso',
            'result' => 1,
            'action' => 'search'
        ]);

    }

}

?>