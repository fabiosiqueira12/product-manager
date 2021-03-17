<?php

namespace App\Controllers;

use App\Core\SendMail;
use App\Core\AuthControl;
use App\Services\SMTPService;
use App\Services\UserService;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Slim\Exception\NotFoundException;

class LoginController extends Controller
{

    public function index(Request $request, Response $response, $args)
    {
        $authControl = new AuthControl();
        if ($authControl->userLogged()){
            $queryParams = $request->getQueryParams();
            $nextUrl = isset($queryParams['next']) && $queryParams['next'] != '' ? $queryParams['next'] : 'dashboard';
            return $response->withRedirect(\base_url_new().$nextUrl,301);
        }
        return $this->view->render($response, '/auth/login.twig', []);
    }

    public function logout(Request $request, Response $response)
    {
        $authControl = new AuthControl();
        if ($authControl->userLogged()){
            $authControl->makeLogout();
            return $response->withRedirect('login');
        }else{
            return $response->withRedirect('/');
        }
    }

    public function login(Request $request, Response $response)
    {
        $body = $request->getParsedBody();
        $email = isset($body['username']) ? $body['username'] : '';
        $senha = isset($body['password']) ? $body['password'] : '';
        $authControl = new AuthControl();
        $result = $authControl->makeLogin($email, $senha);
        return $result;
    }

    public function restaurar(Request $request, Response $response,$args)
    {
        $queryParams = $args;
        if (!isset($queryParams['token']) || $queryParams['token'] == ''){
            throw new NotFoundException($request, $response);
        }
        //CHECK SE o token é válido
        $userService = new UserService();
        $objectRecover = $userService->getRecoverToken($queryParams['token']);
        if ($objectRecover == null || $objectRecover == false){
            throw new NotFoundException($request, $response);
        }
        return $this->view->render($response,'auth/recuperar.twig',[
            'objectRecover' => $objectRecover
        ]);
    }

    public function reset(Request $request, Response $response, $args)
    {
        
        $body = $request->getParsedBody();
        if (!isset($body['token']) || $body['token'] == ""){
            return \throwJsonException('Recarregue essa página ela não funciona mais');
        }
            
        $userService = new UserService();
        $objectRecover = $userService->getRecoverToken($body['token']);
        if ($objectRecover == null || $objectRecover == false){
            return \throwJsonException('Faça o procedimento novamente essa página não funciona mais');
        }
        $user = $userService->returnByParan('id',$objectRecover->id_user);
        if ($user == null || $user == false){
            return \throwJsonException('O usuário não foi encontrado');
        }

        $updatePass = $userService->updatePass($user->getToken(),$body['password']);
        if (!is_bool($updatePass)){
            return $updatePass;  
        }
        
        $userService->removeRecoverToken($body['token']);

        $paramsSuccess = [
            'message' => 'Senha alterada com sucesso',
            'result' => 1
        ];

        $paramsSuccess['action'] = 'goto';
        $paramsSuccess['link'] = '/login';

        return \json($paramsSuccess);
        
    }

    public function recuperar(Request $request, Response $response)
    {
        
        $email = $request->getParsedBodyParam('email');
        if ($email == null || $email == ''){
            return \throwJsonException('Digite um e-mail válido');
        }

        $userService = new UserService();
        $user = $userService->returnByParan('email',$email);
        if ($user == null || $user == false){
            return \throwJsonException('O usuário não foi encontrado na base');
        }

        $tokenRecover = $userService->createRecoverToken($user->getEmail(),$user->getId());
        if (!is_string($tokenRecover)){
            return $tokenRecover;
        }
        
        $baseLink = base_url_new();
        $link = "{$baseLink}recuperar/{$tokenRecover}";

        $stmpService = new SMTPService();
        $smtp = $stmpService->get();
        $sendMail = new SendMail(
            $smtp->host,
            $smtp->username,
            $smtp->port,
            $smtp->pass,
            $smtp->protocol,
            $smtp->autenticar,
            $smtp->email
        );

        $htmlEmail = $this->view->fetch('/auth/email-recuperar.twig',[
            'title' => 'Recuperar Senha',
            'user' => $user,
            'link' => $link
        ]);

        return $sendMail->send(
            $htmlEmail,
            'Recuperação de Senha | Faz Agilizar',
            $user->getEmail(),
            $user->getNome()
        );

    }

    
}

?>