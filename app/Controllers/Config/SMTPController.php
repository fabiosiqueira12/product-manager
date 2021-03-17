<?php

namespace App\Controllers\Config;

use App\Services\SMTPService;
use App\Controllers\Controller;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class SMTPController extends Controller
{

    public function index(Request $request, Response $response, $args)
    {
        $smtpService = new SMTPService();
        $smtp = $smtpService->get();
        return $this->view->render($response, '/pages/config/smtp.twig', ['smtp' => $smtp]);
    }

    public function save(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $smtpService = new SMTPService();
        if ($smtpService->save($body)){
            return json(['message' => 'Salvo com sucesso','result' => 1,'action' => 'reload']);
        }else{
            return json(['message' => 'Erro ao salvar SMTP','result' => 0]);
        }
    }

}

?>