<?php

namespace App\Controllers\Products;

use App\Controllers\Controller;
use App\Models\Product;
use App\Services\Products\CategoryService;
use App\Services\Products\ProductService;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

class IndexController extends Controller{

    private $folder = "pages/products";
    private $urlPaginate = "produtos/paginate";

    public function index(Request $request, Response $response, $args)
    {

        $service = new ProductService();
        $list = $service->paginate([
            'page' => 1,
            'forpage' => 20
        ]);

        $categoriesService = new CategoryService();
        $categories = $categoriesService->paginate([
            'page' => 1,
            'forpage' => 10000
        ])['results'];

        return $this->view->render($response,"{$this->folder}/index.twig",[
            'list' => $list,
            'urlPaginate' => $this->urlPaginate,
            'list_status' => list_status_product(),
            'categories' => $categories
        ]);

    }

    public function search(Request $request, Response $response, $args)
    {

        $body = $request->getParsedBody();
        $page = isset($body['page']) && !empty($body['page']) ? \intval($body['page']) : 1;
        $body['page'] = $page;

        $service = new ProductService();
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
        $service = new ProductService();
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
        
        if (!isset($body['code']) || empty($body['code'])){
            throw new Exception("Você precisa informar o código do produto");
        }
        if (!isset($body['title']) || empty($body['title'])){
            throw new Exception("Você precisa informar o título");
        }

        if (!isset($body['id_category']) || empty($body['id_category'])){
            $body['id_category'] = null;
        }

        if (isset($body['price_seller']) && !empty($body['price_seller'])){
            $body['price_seller'] = str_to_float($body['price_seller']);
        }else{
            $body['price_seller'] = null;
        }

        if (isset($body['price_cost']) && !empty($body['price_cost'])){
            $body['price_cost'] = str_to_float($body['price_cost']);
        }else{
            $body['price_cost'] = null;
        }

        $service = new ProductService();
        $params = [
            'result' => 1,
            'action' => 'search'
        ];
        if (isset($body['id']) && !empty($body['id'])){
            //UPDATE
            $result = $service->update($body);
            $params['message'] = "Atualizado com sucesso";
        }else{
            //SAVE
            $result = $service->save($body);
            $params['message'] = "Salvo com sucesso";
        }

        return \json($params);

    }

    public function delete(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!isset($body['ref']) || empty($body['ref'])){
            throw new Exception("A referência do produto não foi encontrada");
        }
        $service = new ProductService();
        $delete = $service->delete($body['ref']);
        
        return \json([
            'message' => "Removido com sucesso",
            'result' => 1,
            'action' => 'search'
        ]);
    }

    public function active(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!isset($body['ref']) || empty($body['ref'])){
            throw new Exception("A referência do produto não foi encontrada");
        }
        $service = new ProductService();
        $updateStatus = $service->updateStatus(Product::STATUS_ACTIVE,$body['ref']);
        
        return \json([
            'message' => "Ativado com sucesso",
            'result' => 1,
            'action' => 'search'
        ]);
    }


    public function block(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!isset($body['ref']) || empty($body['ref'])){
            throw new Exception("A referência do produto não foi encontrada");
        }
        $service = new ProductService();
        $updateStatus = $service->updateStatus(Product::STATUS_BLOCK,$body['ref']);
        
        return \json([
            'message' => "Bloqueado com sucesso",
            'result' => 1,
            'action' => 'search'
        ]);
    }

    public function setInventory(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!isset($body['id']) || $body['id'] == ""){
            throw new Exception("A referência do produto não foi encontrada");
        }
        if (!isset($body['quant']) || $body['quant'] == ""){
            throw new Exception("Você precisa definir a quantidade que vai adicionar");
        }

        $service = new ProductService();
        $setInventory = $service->setInventory($body['quant'],$body['id']);

        return \json([
            'message' => "Estoque definido com sucesso",
            'result' => 1,
            'action' => 'search'
        ]);
    }

}

?>