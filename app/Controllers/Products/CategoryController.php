<?php

namespace App\Controllers\Products;

use App\Controllers\Controller;
use App\Services\Products\CategoryService;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

class CategoryController extends Controller{

    private $folder = "pages/products/categories";
    private $urlPaginate = "produtos/categorias";

    public function index(Request $request, Response $response, $args)
    {

        $service = new CategoryService();
        $list = $service->paginate([
            'page' => 1,
            'forpage' => 30
        ]);

        return $this->view->render($response,"{$this->folder}/index.twig",[
            'list' => $list,
            'urlPaginate' => $this->urlPaginate
        ]);

    }

    public function search(Request $request, Response $response, $args)
    {

        $body = $request->getParsedBody();
        $page = isset($body['page']) && !empty($body['page']) ? \intval($body['page']) : 1;
        $body['page'] = $page;

        $service = new CategoryService();
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
        $service = new CategoryService();
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
        if (!isset($body['title']) || empty($body['title'])){
            throw new Exception("Você precisa informar o título");
        }
        $body['slug'] = !empty($body['slug']) ? normaliza_slug($body['slug']) : normaliza_slug($body['title']);

        $service = new CategoryService();
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
            throw new Exception("A referência da categoria não foi encontrada");
        }
        $service = new CategoryService();
        $delete = $service->delete($body['ref']);
        
        return \json([
            'message' => "Removido com sucesso",
            'result' => 1,
            'action' => 'search'
        ]);
    }

}

?>