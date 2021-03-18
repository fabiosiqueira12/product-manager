<?php

namespace App\Services\Products;

use App\Services\Service;
use Exception;

class CategoryService extends Service{

    private $fields = "a.*";
    private $forpage = 30;

    function __construct() {
        parent::__construct();
        $this->setTable("product_category");
        $this->setPrefix("a");
    }

    /**
     * Faz Paginação das categorias
     *
     * @param array $body
     * @param boolean $isAPI
     * @return array
     */
    public function paginate($body, $order = "a.order_by ASC",$isAPI = false)
    {

        $completeWhere = "";
        $page = isset($body['page']) ? $body['page'] : 1;
        $forpageApi = isset($body['forpage']) && $body['forpage'] != '' ? \intval($body['forpage']) : $this->forpage;

        $innerTables = [];
        $paginateResults = $this->genericPaginate($this->fields,$innerTables,$completeWhere,$page,$forpageApi,$order);
        return $paginateResults;

    }

    /**
     * Verifica se já está cadastrado um texto com determinado REF
     *
     * @param string $slug
     * @param int $id
     * @return boolean
     */
    private function checkRef($slug,$id = null)
    {

        $stmt = $this->PDO->prepare(
            "SELECT id FROM {$this->table} WHERE slug = :slug "
        );
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_OBJ);

        if ($id != null){
            if (count($results) > 0){
                $exist = false;
                foreach($results as $value){
                    if ($value->id != $id){
                        $exist = true;
                        break;
                    }
                }
                return $exist;
            }else{
                return false;
            }
        }else{
            if (count($results) > 0){
                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * Salva a categoria
     *
     * @param array $body
     * @return object
     */
    public function save($body)
    {
        if ($this->checkRef($body['slug'],null)){
            throw new Exception("Já existe uma categoria com essa referência");
        }
        return $this->genericeSave($body,['id'],false);
    }

    /**
     * Atualiza a categoria
     *
     * @param array $body
     * @return object
     */
    public function update($body)
    {
        if ($this->checkRef($body['slug'],$body['id'])){
            throw new Exception("Já existe uma categoria com essa referência");
        }
        return $this->genericUpdate($body,['id'],true,false);
    }

    /**
     * Remove do banco a categoria
     *
     * @param int $id
     * @return object
     */
    public function delete($id){
        return $this->genericDelete($id);
    }

}

?>