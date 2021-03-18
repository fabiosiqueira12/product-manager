<?php

namespace App\Controllers\Products;

use App\Controllers\Controller;
use App\Services\Products\HistoryProductService;
use Slim\Http\Request;
use Slim\Http\Response;

class HistoryController extends Controller{

    private $folder = "pages/products/history";
    private $urlPaginate = "produtos/historico/paginate";

    public function index(Request $request, Response $response, $args)
    {

        $service = new HistoryProductService();
        $list = $service->paginate([
            'page' => 1,
            'forpage' => 20
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

        $service = new HistoryProductService();
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
        $service = new HistoryProductService();
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

}

?>