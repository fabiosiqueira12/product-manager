<?php 

namespace App\Middlewares;

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Middleware que protege rotas que devem ser acessadas pela a API
 * 
 * @author Fábio Siqueira
 */
class ApiMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next) 
    {
        
        $header = $request->getHeaders();
            
        if (!isset($header['HTTP_TOKENACCESS']) || count($header['HTTP_TOKENACCESS']) == 0 || $header['HTTP_TOKENACCESS'][0] == ''){
            throw new Exception("Você precisa informar o token de acesso");
        }

        $token_access = $header['HTTP_TOKENACCESS'][0];

        $dados = \return_dados();

        if ($dados->token_access != $token_access){
            throw new Exception('Token de acesso inválido');
        }
        
        return $next($request, $response);

    }
}
