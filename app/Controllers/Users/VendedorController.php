<?php

namespace App\Controllers\Users;

use App\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Slim\Http\Request;
use Slim\Http\Response;

class VendedorController extends Controller
{

    public function index(Request $request, Response $response, $args)
    {
        $userService = new UserService();
        $users = $userService->paginateVendedores([
            'page' => 1,
            'forpage' => 20,
            'id_type' => User::TYPE_VENDEDOR,
            'id_empresa' => \company_logged()->getId()
        ]);
        $paginate = \mount_paginate($users['totalPages'],1);
        return $this->view->render($response,'pages/vendedores/index.twig',[
            'users' => $users['results'],
            'totalPages' => $users['totalPages'],
            'totalCount' => $users['totalCount'],
            'inicio' => $paginate['inicio'],
            'fim' => $paginate['fim'],
            'type' => User::TYPE_VENDEDOR
        ]);
    }

    public function search(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $page = isset($body['page']) ? $body['page'] : 1;
        $userService = new UserService();
        $users = $userService->paginateVendedores($body);
        $paginate = \mount_paginate($users['totalPages'],$page);
        return $this->view->render($response, '/pages/vendedores/_box.twig', [
            'users' => $users['results'],
            'totalPages' => $users['totalPages'],
            'totalCount' => $users['totalCount'],
            'actual' => $page,
            'inicio' => $paginate['inicio'],
            'fim' => $paginate['fim']
        ]);
    }

    public function paginate(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $page = isset($body['page']) ? $body['page'] : 1;
        $userService = new UserService();
        $users = $userService->paginateVendedores($body);
        $paginate = \mount_paginate($users['totalPages'],$page);
        return $this->view->render($response, '/pages/vendedores/_box.twig', [
            'users' => $users['results'],
            'totalPages' => $users['totalPages'],
            'totalCount' => $users['totalCount'],
            'actual' => $page,
            'inicio' => $paginate['inicio'],
            'fim' => $paginate['fim']
        ]);
    }

}

?>