<?php

namespace App\Controllers\Config;

use App\Services\MenuService;
use App\Controllers\Controller;
use Exception;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class MenuController extends Controller
{

    public function index(Request $request, Response $response, $args)
    {
        $menusService = new MenuService();
        $getMenus = $menusService->get();
        $menus = [];
        foreach ($getMenus as $key => $value) {
            if ($value->root == 0){
                $object = (object) ['principal' => null,'submenus' => []];
                if ($value->permissions == ''){
                    $value->permissions = [];
                }else{
                    $value->permissions = explode(',',$value->permissions);
                }
                if ($value->tipos_empresa == ''){
                    $value->tipos_empresa = [];
                }else{
                    $value->tipos_empresa = explode(',',$value->tipos_empresa);
                }
                $value->variations = explode('&',$value->variations);
                $object->principal = $value;
                $menus[] = $object;
            }
        }

        foreach($menus as $key => $value){
            foreach($getMenus as $k => $v){
                if ($v->root == $value->principal->id){
                    if ($v->permissions == ''){
                        $v->permissions = [];
                    }else{
                        $v->permissions = explode(',',$v->permissions);
                    }
                    if ($v->tipos_empresa == ''){
                        $v->tipos_empresa = [];
                    }else{
                        $v->tipos_empresa = explode(',',$v->tipos_empresa);
                    }
                    $v->variations = explode(',',$v->variations);
                    $value->submenus[] = $v;
                }
            }
        }

        return $this->view->render($response, '/pages/config/menu.twig', [
            'menus' => $menus
        ]);
    }

    public function save(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (isset($body['permissions']) && $body['permissions'] != ""){
            $body['permissions'] = implode(',',$body['permissions']);
        }else{
            $typesUsers = \getTypesUsers();
            $keysTypes = array_keys($typesUsers);
            $body['permissions'] = implode(',',$keysTypes);
        }
        if (isset($body['id']) && $body['id'] != ''){
            $menuService = new MenuService();
            if($menuService->edit($body)){
                return json(['message' => 'Atualizado com sucesso','result' => 1,'action' => 'reload']);
            }else{
                return json(['message' => 'Erro ao atualizar menu','result' => 0]);
            }
        }else{
            $menuService = new MenuService();
            if($menuService->save($body)){
                return json(['message' => 'Salvo com sucesso menu','result' => 1,'action' => 'reload']);
            }else{
                return json(['message' => 'Erro ao salvar menu','result' => 0]);
            }
        }
    }

    public function remove(Request $request, Response $response, $args)
    {
        $ref = $request->getParsedBodyParam('ref');
        if (empty($ref)){
            throw new Exception("A referência do menu não foi encontrada");
        }
        $menuService = new MenuService();
        if (!$menuService->delete($ref)){
            throw new Exception("Erro ao remover menu");
        }
        return json(['message' => 'Menu removido','result' => 1,'action' => 'reload']);
    }

}

?>