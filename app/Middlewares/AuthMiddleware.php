<?php 

namespace App\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Core\AuthControl;
use Slim\Exception\NotFoundException;

/**
 * Middleware que protege rotas que devem ser acessadas apenas por usuários autenticados
 * 
 * @author Walison Filipe <walisonfilipe@hotmail.com>
 */
class AuthMiddleware
{
    /** @var string nome da rota para redirecionamento */
    private $redirecTo;

    public function __construct(string $redirecTo = null) 
    {
        $this->redirecTo = $redirecTo;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {

        //Verifica se o usuário está logado
        $authControl = new AuthControl();
        if (!$authControl->userLogged()){
            if ($request->isPost() || $request->isPut()){
                return $response->withJson([
                    'message' => 'Sessão expirada, faça o login novamente',
                    'result' => 0
                ]);
            }else{
                return $response->withRedirect(\base_url_new().'login',302);
            }
        }

        //Verifica permissões do menu
        if ($request->getMethod() == "GET"){
            $controller = $request->getUri()->getPath();
            if (!\verify_permission($controller)){
                throw new NotFoundException($request, $response->withStatus(405));
            }
        }

        return $next($request, $response);
        
    }
}

