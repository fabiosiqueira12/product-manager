<?php

namespace App\Controllers\Config;

use App\Controllers\Controller;
use App\Services\Users\TypeUserService;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Slim\Exception\NotFoundException;

class TypeController extends Controller
{

    public function index(Request $request, Response $response, $args)
    {
        if (!isset($args['type']) || $args['type'] == ""){
            throw new NotFoundException($request, $response);
        }
        switch ($args['type']) {
            case 'company':
                throw new NotFoundException($request,$response);
                break;
            default:
                $service = new TypeUserService();
                $types = $service->getAll();
                $title = 'Usuários';
                break;
        }
        return $this->view->render($response, '/pages/config/types.twig', [
            'title' => $title,
            'types' => $types,
            'type_arg' => $args['type']
        ]);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!isset($args['type']) || $args['type'] == ""){
            return \throwJsonException('O tipo para ser cadastrado não foi informado');
        }
        switch ($args['type']) {
            case 'company':
                $service = null;
                break;
            default:
                $service = new TypeUserService();
                break;
        }
        if (!isset($body['ref']) || $body['ref'] == ""){
            return \throwJsonException('A referência do tipo não foi encontrada');
        }
        $delete = $service->delete($body['ref']);
        return \json([
            'message' => 'Deletado com sucesso',
            'result' => 1,
            'action' => 'reload'
        ]);
    }

    public function save(Request $request, Response $response , $args)
    {
        $body = $request->getParsedBody();
        if (!isset($args['type']) || $args['type'] == ""){
            return \throwJsonException('O tipo para ser cadastrado não foi informado');
        }
        switch ($args['type']) {
            case 'company':
                $service = null;
                break;
            default:
                $service = new TypeUserService();
                break;
        }
        $body['ref'] = normaliza_slug($body['ref']);
        if (isset($body['id']) && $body['id'] != ""){
            //UPDATE
            $update = $service->update($body);
            if (\is_bool($update)){
                return json(['message' => 'Atualizado com sucesso','result' => 1,'action' => 'new']);
            }else{
                return $update;
            }
        }else{
            //SAVE
            $save = $service->save($body);
            if (\is_bool($save)){
                return json(['message' => 'Salvo com sucesso','result' => 1,'action' => 'new']);
            }else{
                return $save;
            }
        }
    }

}

?>