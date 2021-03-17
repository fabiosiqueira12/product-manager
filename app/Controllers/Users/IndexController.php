<?php

namespace App\Controllers\Users;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Core\AuthControl;
use App\Services\UserService;
use App\Controllers\Controller;
use App\Services\TypeUserService;
use Slim\Exception\NotFoundException;

class IndexController extends Controller
{

    private $folder = "pages/users";

    public function index(Request $request, Response $response, $args)
    {

        if (!isset($args['type']) && $args['type'] == ""){
            throw new NotFoundException($request, $response);
        }

        $typeService = new TypeUserService();
        $typeObject = $typeService->getByParan('ref',$args['type']);
        if ($typeObject == null){
            throw new NotFoundException($request, $response);
        }
        $typesUsers = \getTypesUsers();
        if (!array_key_exists($typeObject->id,$typesUsers)){
            throw new NotFoundException($request, $response);
        }

        $userService = new UserService();
        $page = 1;
        $users = $userService->paginate(['id_type' => $typeObject->id]);
        $paginate = \mount_paginate($users['totalPages'],1);
        return $this->view->render($response,"{$this->folder}/index.twig",[
            'users' => $users,
            'paginate' => $paginate,
            'typeObject' => $typeObject,
            'type' => $typeObject->id
        ]);
        
    }

    public function changeType(Request $request, Response $response, $args)
    {

        $body = $request->getParsedBody();
        if (!isset($body['token']) || $body['token'] == ''){
            return \throwJsonException("A referência do usuário não foi encontrada");
        }
        if (!isset($body["type"]) || $body['type'] == ""){
            return \throwJsonException("Você precisa informar o tipo para alterar");
        }

        $userService = new UserService();
        $result = $userService->changeType($body['type'],$body['token']);
        if (!is_bool($result)){
            return $result;
        }
        return \json([
            'message' => 'Alterado com sucesso',
            'result' => 1,
            'action' => 'search'
        ]);

    }

    public function desactive(Request $request, Response $response, $args)
    {

        $ref = $request->getParsedBodyParam('ref');
        if ($ref == null || $ref == ''){
            return \throwJsonException('A referência do usuário não foi encontrada');
        }
        if (!user_logged()->isAdmin()){
            return \throwJsonException('Você naõ tem permissão para essa ação');
        }
        
        $userService = new UserService();
        $updateStatus = $userService->updateStatus(0,$ref);
        if (!\is_bool($updateStatus)){
            return $updateStatus;   
        }
        return \json([
            'message' => 'Usuário desbloqueado',
            'result' => 1,
            'action' => 'search'
        ]);
    }

    public function active(Request $request, Response $response, $args)
    {

        $ref = $request->getParsedBodyParam('ref');
        if ($ref == null || $ref == ''){
            return \throwJsonException('A referência do usuário não foi encontrada');
        }
        if (!user_logged()->isAdmin()){
            return \throwJsonException('Você naõ tem permissão para essa ação');
        }
        
        $userService = new UserService();
        $updateStatus = $userService->updateStatus(1,$ref);
        if (!\is_bool($updateStatus)){
            return \throwJsonException('Usuário não encontrado para desbloquear');
        }
        return json([
            'message' => 'Usuário desbloqueado',
            'result' => 1,
            'action' => 'search'
        ]);
    }

    public function save(Request $request, Response $response, $args)
    {

        $userService = new UserService();
        $body = $request->getParsedBody();

        if ($userService->checkEmail($body['email'],isset($body['token']) ? $body['token'] : '')){
            return json(['message' => 'Já existe um usuário com esse e-mail','result' => 0]);
        }
        if ($userService->checkUser($body['login'],isset($body['token']) ? $body['token'] : '')){
            return json(['message' => 'Já existe um usuário com esse login','result' => 0]);
        }

        if (isset($body['senha']) && $body['senha'] != ""){
            if (strlen($body['senha']) < 6) {
                return \throwJsonException('Você precisa digitar no mínimo 6 caracteres');
            }
            if ($body['senha'] != $body['rsenha']) {
                return \throwJsonException('As senhas são incompatíveis');
            }
        }

        //Verifica se tá editando
        if (isset($body['token']) && $body['token'] != '') {

            //Aqui vai editar
            $user = $userService->returnByParan('token',$body['token']);
            if ($user == null || $user == false){
                return \throwJsonException('Não foi possível encontrar o usuário, recarregue a página');
            }
            $body['id'] = $user->getId();
            $edit = $userService->update($body);
            if (!is_bool($edit)) {
                return $edit;
            }
            if ($user->getId() == user_logged()->getId()) {
                $userSession = user_logged();
                $userSession->setLogin($body['login']);
                $userSession->setEmail($body['email']);
                $userSession->setNome($body['nome']);
                $userSession->setTelefone($body['telefone']);
                $authControl = new AuthControl();
                $authControl->saveJustUser($userSession);
            }
            return json([
                'message' => 'Atualizado com sucesso',
                'result' => 1,
                'action' => 'search'
            ]);

        } else {
            if ($body['login'] == "" || strlen($body['login']) > 20){
                return \throwJsonException('Login Inválido, digite no máximo 20 caracteres');
            }

            //Aqui vai salvar o usuário
            $save = $userService->save($body);
            if (!is_string($save)) {
                return $save;
            }
            
            return json([
                'message' => 'Salvo com sucesso',
                'result' => 1,
                'action' => 'search'
            ]);
        }
    }

    public function search(Request $request, Response $response, $args)
    {

        $body = $request->getParsedBody();
        $page = isset($body['page']) && !empty($body['page']) ? \intval($body['page']) : 1;
        $body['page'] = $page;
        $userService = new UserService();
        $users = $userService->paginate($body);
        $paginate = \mount_paginate($users['totalPages'],$page);
        
        return $this->view->render($response, 'helpers/_box-paginate.twig', [
            "folderList" => "{$this->folder}/_list.twig",
            "paramsList" => [
                'users' => $users['results'],
                'type' => $body['id_type']
            ],
            'list' => $users,
            'paginate' => $paginate,
            "urlPaginate" => "usuarios/paginate"
        ]);

    }

    public function paginate(Request $request, Response $response, $args)
    {

        $body = $request->getParsedBody();
        $page = isset($body['page']) && !empty($body['page']) ? \intval($body['page']) : 1;
        $body['page'] = $page;
        $userService = new UserService();
        $users = $userService->paginate($body);
        $paginate = \mount_paginate($users['totalPages'],$page);

        return $this->view->render($response, 'helpers/_box-paginate.twig', [
            "folderList" => "{$this->folder}/_list.twig",
            "listParams" => [
                'users' => $users['results'],
                'type' => $body['id_type']
            ],
            'list' => $users,
            'paginate' => $paginate,
            "urlPaginate" => "usuarios/paginate"
        ]);
        
    }

}