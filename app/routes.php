<?php

    $app->add(function ($req, $res, $next) {
        $response = $next($req, $res);
        if (strpos($req->getUri()->getPath(), 'api/') !== false) {
            return $response
                ->withHeader('Content-Type', 'application/json;charset=utf-8')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization,tokenaccess,si-userip,si-domain,si-useragent,gnc-login-token')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        }else{
            return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization,tokenaccess,si-userip,si-domain,si-useragent,gnc-login-token')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        }
    });

    //Routes img
    $app->get('/img/crop', function (Slim\Http\Request $request, Slim\Http\Response $response, $args) {

        /* return $response->withRedirect(base_url_new() . 'assets/uploads/' . $request->getQueryParam('img')); */
        
        // Set source filesystem
        $source = new League\Flysystem\Filesystem(
            new League\Flysystem\Adapter\Local('assets/uploads')
        );

        // Set cache filesystem
        $cache = new League\Flysystem\Filesystem(
            new League\Flysystem\Adapter\Local('assets/cache')
        );

        // Set image manager
        $imageManager = new Intervention\Image\ImageManager([
            'driver' => 'gd',
        ]);

        // Set manipulators
        $manipulators = [
            new League\Glide\Manipulators\Crop(),
            new League\Glide\Manipulators\Size(2000*2000),
            new League\Glide\Manipulators\Background(),
            new League\Glide\Manipulators\Encode()
        ];

        // Set API
        $api = new League\Glide\Api\Api($imageManager, $manipulators);

        // Setup Glide server
        $server = new League\Glide\Server($source,$cache, $api);

        // Set response factory
        $server->setResponseFactory(new League\Glide\Responses\SlimResponseFactory());
        try {
            return $server->getImageResponse($request->getQueryParam('img'), $request->getQueryParams());
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    });

     //ROUTES API
     $app->group('/api', function () use ($app) {
        
    })->add(new \App\Middlewares\ApiMiddleware);

    //Routes AUTH
    $app->post('/login', \App\Controllers\LoginController::class . ':login')->setName('login.login');
    $app->get('/logout', \App\Controllers\LoginController::class . ':logout')->setName('login.logout');
    $app->get('/login', \App\Controllers\LoginController::class . ':index')->setName('login');
    $app->get('/cadastro', \App\Controllers\RegisterController::class . ':index')->setName('cadastro.anunciante.index');
    $app->post('/recuperar', \App\Controllers\LoginController::class . ':recuperar')->setName('login.recuperar');
    $app->get('/recuperar/{token}', \App\Controllers\LoginController::class . ':restaurar')->setName('login.restaurar');
    $app->post('/reset-password', \App\Controllers\LoginController::class . ':reset')->setName('login.reset');

    $app->post('/clear-cache', \App\Controllers\HomeController::class . ':clearCache')->setName('clearCache');

    // Routes GET
    $app->get('/', \App\Controllers\LoginController::class . ':index')->setName('index')->add(new \App\Middlewares\AuthMiddleware);
    $app->get('/perfil', \App\Controllers\Users\ProfileController::class . ':index')->setName('perfil')->add(new \App\Middlewares\AuthMiddleware);
    $app->get('/dashboard', \App\Controllers\HomeController::class . ':index')->setName('home')->add(new \App\Middlewares\AuthMiddleware);

    //Rota de usuários
    $app->group('/usuarios',function () use ($app){
        
        $this->get('/{type}', \App\Controllers\Users\IndexController::class . ':index')->setName('users');
        $this->post('/save', \App\Controllers\Users\IndexController::class . ':save')->setName('users.save');
        $this->post('/change-type', \App\Controllers\Users\IndexController::class . ':changeType')->setName('users.changeType');
        $this->post('/desativar', \App\Controllers\Users\IndexController::class . ':desactive')->setName('users.desactive');
        $this->post('/ativar', \App\Controllers\Users\IndexController::class . ':active')->setName('users.active');
        $this->post('/search', \App\Controllers\Users\IndexController::class . ':search')->setName('usuarios.search');
        $this->post('/paginate', \App\Controllers\Users\IndexController::class . ':paginate')->setName('usuarios.paginate');

    })->add(new \App\Middlewares\AuthMiddleware);

    //ROUTES MENUS GROUP
    $app->group('/menu', function (){

        $this->get('', App\Controllers\Config\MenuController::class . ':index')->setName('menu.index');
        $this->post('/save', App\Controllers\Config\MenuController::class . ':save')->setName('menu.save');
        $this->post('/delete', App\Controllers\Config\MenuController::class . ':remove')->setName('menu.remove');

    })->add(new \App\Middlewares\AuthMiddleware);

    //ROUTES DADOS GROUP
    $app->group('/dados', function (){
        
        $this->get('', App\Controllers\Config\DadosController::class . ':index')->setName('dados.index');
        $this->post('/save', App\Controllers\Config\DadosController::class . ':save')->setName('dados.save');

    })->add(new \App\Middlewares\AuthMiddleware);

    //ROUTES SMTP
    $app->get('/smtp', App\Controllers\Config\SMTPController::class . ':index')->setName('smtp.index');
    $app->post('/new-smtp', App\Controllers\Config\SMTPController::class . ':save')->setName('smtp.save');

    //ROUTES TIPOS GROUP
    $app->group('/tipos', function (){

        $this->get('/{type}', \App\Controllers\Config\TypeController::class . ':index')->setName('types.index');
        $this->post('/{type}/save', \App\Controllers\Config\TypeController::class . ':save')->setName('types.save');
        $this->post('/{type}/delete', \App\Controllers\Config\TypeController::class . ':delete')->setName('types.delete');

    })->add(new \App\Middlewares\AuthMiddleware);

    //APP GROUP REPOSITORIO
    $app->group('/repositorio', function() {

        $this->get('', \App\Controllers\RepositorioController::class . ':index')->setName('repositorio.index');
        $this->post('/search', \App\Controllers\RepositorioController::class . ':search')->setName('repositorio.search');
        $this->post('/paginate', \App\Controllers\RepositorioController::class . ':paginate')->setName('repositorio.paginate');
        $this->post('/save', \App\Controllers\RepositorioController::class . ':save')->setName('repositorio.save');
        $this->post('/delete', \App\Controllers\RepositorioController::class . ':delete')->setName('repositorio.delete');

    })->add(new \App\Middlewares\AuthMiddleware);

    //APP GROUP PRODUTOS
    $app->group('/produtos', function() use ($app) {

        $this->get('', \App\Controllers\Products\IndexController::class . ':index')->setName('products.index');
        $this->post('/search', \App\Controllers\Products\IndexController::class . ':search')->setName('products.search');
        $this->post('/paginate', \App\Controllers\Products\IndexController::class . ':paginate')->setName('products.paginate');
        $this->post('/save', \App\Controllers\Products\IndexController::class . ':save')->setName('products.save');
        $this->post('/delete', \App\Controllers\Products\IndexController::class . ':delete')->setName('products.delete');
        $this->post('/active', \App\Controllers\Products\IndexController::class . ':active')->setName('products.active');
        $this->post('/block', \App\Controllers\Products\IndexController::class . ':block')->setName('products.block');
        $this->post('/inventory/add', \App\Controllers\Products\IndexController::class . ':setInventory')->setName('produtos.inventory.set');

        //ROUTES CATEGORIAS GROUP
        $app->group("/categorias",function(){
            $this->get('', \App\Controllers\Products\CategoryController::class . ':index')->setName('products.categories.index');
            $this->post('/search', \App\Controllers\Products\CategoryController::class . ':search')->setName('products.categories.search');
            $this->post('/paginate', \App\Controllers\Products\CategoryController::class . ':paginate')->setName('products.categories.paginate');
            $this->post('/save', \App\Controllers\Products\CategoryController::class . ':save')->setName('products.categories.save');
            $this->post('/delete', \App\Controllers\Products\CategoryController::class . ':delete')->setName('products.categories.delete');    
        });

        //ROUTES HISTÓRICO GROUP
        $app->group("/historico",function(){
            $this->get('/{code}', \App\Controllers\Products\HistoryController::class . ':index')->setName('products.history.index');
            $this->post('/search', \App\Controllers\Products\HistoryController::class . ':search')->setName('products.categories.search');
            $this->post('/paginate', \App\Controllers\Products\HistoryController::class . ':paginate')->setName('products.categories.paginate');
        });

    })->add(new \App\Middlewares\AuthMiddleware);

    //APP CONSUMIDORES SERVICE
    $app->group('/consumidores', function (){

        $this->get('', \App\Controllers\ConsumerController::class . ':index')->setName('consumers.index');
        $this->post('/paginate', \App\Controllers\ConsumerController::class . ':paginate')->setName('consumers.paginate');
        $this->post('/search', \App\Controllers\ConsumerController::class . ':search')->setName('consumers.search');
        $this->post('/save', \App\Controllers\ConsumerController::class . ':save')->setName('consumers.save');
        $this->post('/delete', \App\Controllers\ConsumerController::class . ':delete')->setName('consumers.delete');

    })->add(new \App\Middlewares\AuthMiddleware);

    //APP ORDERS SERVICE
    $app->group("/pedidos",function() use ($app){

        $this->get('', \App\Controllers\Orders\IndexController::class . ':index')->setName('orders.index');
        $this->post('/paginate', \App\Controllers\Orders\IndexController::class . ':paginate')->setName('orders.paginate');
        $this->post('/search', \App\Controllers\Orders\IndexController::class . ':search')->setName('orders.search');
        $this->post('/save', \App\Controllers\Orders\IndexController::class . ':save')->setName('orders.save');
        $this->post('/delete', \App\Controllers\Orders\IndexController::class . ':delete')->setName('orders.delete');
        $this->get('/detalhes/{code}', \App\Controllers\Orders\IndexController::class . ':details')->setName('orders.details');

        $app->group('/produtos', function (){
            $this->post('/delete', \App\Controllers\Orders\ProductsController::class . ':delete')->setName('orders.products.delete');
            $this->post('/consultar', \App\Controllers\Orders\ProductsController::class . ':consultar')->setName('orders.products.consultar');
            $this->post('/update-estoque', \App\Controllers\Orders\ProductsController::class . ':updateEstoque')->setName('orders.products.updateEstoque');
            $this->post('/search', \App\Controllers\Orders\ProductsController::class . ':search')->setName('orders.products.search');
            $this->post('/add-many', \App\Controllers\Orders\ProductsController::class . ':addMany')->setName('orders.products.add_many');
        });

    });

?>