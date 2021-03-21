<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers\Controller;
use App\Services\ConsumerService;
use Exception;

class ConsumerController extends Controller{

    private $folder = "pages/consumers";
    private $urlPaginate = "consumidores/paginate";

    public function index(Request $request, Response $response, $args)
    {

        $service = new ConsumerService();
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

        $service = new ConsumerService();
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

    public function paginate(Request $request, Response $response, $args)
    {

        $body = $request->getParsedBody();
        $page = isset($body['page']) && !empty($body['page']) ? \intval($body['page']) : 1;
        $body['page'] = $page;
        $service = new ConsumerService();
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
        if (isset($body['cpf']) && !empty($body['cpf'])){
            if (!\validate_cpf($body['cpf'])){
                throw new Exception("O CPF informado é inválido");
            }
        }
        if (isset($body['cnpj']) && !empty($body['cnpj'])){
            if (!\validate_cnpj($body['cnpj'])){
                throw new Exception("O CNPJ informado é inválido");
            }
        }

        $params = [
            'result' => 1,
            'action' => 'search'
        ];
        $service = new ConsumerService();

        if (isset($body['id']) && !empty($body['id'])){
            //UPDATE
            $params['message'] = "Atualizado com sucesso";
            $result = $service->update($body);
        }else{
            //SAVE
            $params['message'] = "Salvo com sucesso";
            $result = $service->save($body);
        }

        return \json($params);

    }

    public function delete(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!isset($body['id']) || empty($body['id'])){
            throw new Exception("A referência do consumidor não foi encontrada");
        }
        $service = new ConsumerService();
        $delete = $service->delete($body['ref']);
        return \json([
            'message' => 'Removido com sucesso',
            'result' => 1,
            'action' => 'search'
        ]);
    }

}

?>