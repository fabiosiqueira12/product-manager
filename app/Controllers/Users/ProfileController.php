<?php

namespace App\Controllers\Users;

use App\Core\AuthControl;
use App\Controllers\Controller;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class ProfileController extends Controller
{

    public function index(Request $request, Response $response, $args)
    {
        $authControl = new AuthControl();
        if (!$authControl->userLogged()){
            return $response->withRedirect(\base_url_new() . 'login',301);
        }
        return $this->view->render($response, '/pages/profile.twig', []);
    }

}

?>