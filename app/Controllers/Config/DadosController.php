<?php

namespace App\Controllers\Config;

use App\Core\AuthControl;
use App\Services\DadosService;
use App\Controllers\Controller;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class DadosController extends Controller
{

    public function index(Request $request, Response $response, $args)
    {
        $authControl = new AuthControl();
        if (!$authControl->userLogged()){
            return $response->withRedirect(\base_url_new() . 'login',301);
        }
        $dadosService = new DadosService();
        return $this->view->render($response, '/pages/config/dados.twig', [
            'dataConfig' => $dadosService->getDados()
        ]);
    }

    public function save(Request $request, Response $response , $args)
    {
        $authControl = new AuthControl();
        if (!$authControl->userLogged()){
            return \throwJsonException('Sua sessão expirou, faça o login novamente');
        }
        $body = $request->getParsedBody();
        $dadosService = new DadosService();
        $params = [
            'message' => 'Salvo com sucesso',
            'result' => 1,
            'action' => 'new'
        ];
        if (isset($body['id']) && $body['id'] != ''){
            $result = $dadosService->edit($body);
        }else{
            $result = $dadosService->save($body);
        }

        if (!is_int($result)){
            return $result;
        }
        return \json($params);
        
    }

}

?>