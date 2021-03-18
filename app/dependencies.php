<?php

use App\Services\AtualizacoesService;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Http\Environment;
use Slim\Views\TwigExtension;
// DIC configuration

$container = $app->getContainer();
$container['upload_directory'] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads'; //Diretório para upar as imagens

// Register component on container
$container['view'] = function ($container) {
    $whitelist = array(
        '127.0.0.1',
        '::1',
        'localhost'
    );
    $view = new Twig('templates', [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new Environment($_SERVER));
    $view->addExtension(new TwigExtension($router, $uri));

    $function = new Twig_SimpleFunction('user_logged', function () {
        return user_logged();    
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('is_image', function ($type) {
        return is_image($type);    
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('image', function ($url,$parans,$withPath = false) {
        return image($url,$parans,$withPath);    
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('base_url_new', function () {
        return base_url_new();    
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('return_dados', function () {
        return return_dados();    
    });
    $view->getEnvironment()->addFunction($function);
    
    $function = new Twig_SimpleFunction('format_peso', function ($peso) {
        return format_peso($peso);    
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('price_format', function ($number,$withStr = true) {
        return price_format($number,$withStr);    
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('price_format2', function ($number) {
        return price_format2($number);    
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('return_months', function ($month = -1) {
        return return_months($month);
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('in_array', function ($key,$array) {
        if (!is_array($array)){
            return true;
        }
        return in_array($key,$array);
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('get_menus', function () {
        return \return_menus();
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('convertToHoursMins', function ($time) {
        return convertToHoursMins($time);
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('dias_semana', function () {
        return \get_dias_semana();
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('dias_semana_abrev', function () {
        $dias = \get_dias_semana_abrev();
        return $dias;
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('format_bytes', function ($bytes) {
        return format_bytes($bytes);
    });
    $view->getEnvironment()->addFunction($function);
    
    $function = new Twig_SimpleFunction('link_whatsapp', function ($phone) {
        return link_whatsapp($phone);
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('types_users', function () {
        return getTypesUsers();
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('asset', function ($file) {
        return \asset($file);
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('format_address', function ($address) {
        return \format_address($address);
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('formattedSizeFile', function ($size) {
        return formattedSizeFile($size);
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('company_logged', function () {
        return company_logged();
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('return_type', function ($type) {
        return return_type($type);
    });
    $view->getEnvironment()->addFunction($function);

    $function = new Twig_SimpleFunction('folder_paginate_box', function () {
        return \folder_paginate_box();
    });
    $view->getEnvironment()->addFunction($function);

    return $view;

};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        if ($request->isPost()){

            //method POST
            return $response->withStatus(405)
            ->withHeader('Content-Type', 'application/json')
            ->write(\json_encode([
                'message' => "Você não tem permissão para essa ação",
                'result' => 0
            ]));

        }else{
            if (strpos($request->getUri()->getPath(), 'api/') !== false) {
                return $response->withStatus(405)
                ->withHeader('Content-Type', 'application/json')
                ->write(\json_encode([
                    'message' => "Você não tem permissão para essa ação",
                    'result' => 0
                ]));
            }
            //METHOD GET
            $html = $c['view']->fetch('405.twig',[
                'message' => "Você não tem permissão para acessar essa rota"
            ]);
            return $response->withStatus(405)
            ->withHeader('Content-Type', 'text/html;')
            ->write($html);
            
        }
    };
};

//Override the default Not Found Handler
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        if ($request->isPost()){
            return $response->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->write(\json_encode(['message' => 'Essa rota não existe','result' => 0]));
        }else{
            if (strpos($request->getUri()->getPath(), 'api/') !== false) {
                return $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json')
                ->write(\json_encode([
                    'message' => "Essa rota foi removida ou não existe",
                    'result' => 0
                ]));
            }
            return $c['view']->render($response->withStatus(404), '404.twig',[
                'without_link' => false
            ]);
        }
    };
};

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        
        if ($request->isPost() || strpos($request->getUri()->getPath(), 'api/') !== false){
            //method POST OR API ROUTE
            return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(\json_encode(['message' => $exception->getMessage(),'result' => 0]));
        }

        //METHOD GET
        $html = $c['view']->fetch('500.twig',['message' => $exception->getMessage(),'trace' => $exception->getTrace()]);
        return $response->withStatus(500)
        ->withHeader('Content-Type', 'text/html;')
        ->write($html);
    };
};