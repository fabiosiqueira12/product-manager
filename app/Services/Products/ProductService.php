<?php

namespace App\Services\Products;

use App\Models\Product;
use App\Services\Service;
use Exception;

class ProductService extends Service{

    private $fields = "a.id,a.token,a.code,a.title,a.description,a.date_insert,a.inventory,a.status,a.image,a.price_cost,a.price_seller,a.price_money,a.specification,
    b.id AS id_category,b.title AS title_category,b.slug AS slug_category";
    private $forpage = 20;
    private $tableCategory = "product_category";
    private $tableHistory = "product_history";
    private $innerTables = [];

    public function __construct()
    {
        parent::__construct();
        $this->setTable("product");
        $this->setPrefix("a");
        $this->innerTables[] = " LEFT JOIN {$this->tableCategory} AS b ON b.id = a.id_category ";
    }

    /**
     * Salva o produto no banco
     *
     * @param array $body
     * @return object
     */
    public function save($body)
    {
        if ($this->checkCode($body['code'],null)){
            throw new Exception("Já existe um produto com esse código");
        }
        $body['token'] = create_token();
        return $this->genericeSave($body,['id'],true);
    }

    /**
     * Verifica se já está cadastrado um texto com determinado REF
     *
     * @param string $code
     * @param int $id
     * @return boolean
     */
    private function checkCode($code,$id = null)
    {

        $stmt = $this->PDO->prepare(
            "SELECT id FROM {$this->table} WHERE code = :code "
        );
        $stmt->bindValue(':code', $code);
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
     * Atualiza o produto no banco
     *
     * @param array $body
     * @return object
     */
    public function update($body)
    {
        if ($this->checkCode($body['code'],$body['id'])){
            throw new Exception("Já existe um produto com esse código");
        }
        $excludeKeys = ['id','token','inventory'];
        if (!isset($body['image']) || empty($body['image'])){
            $excludeKeys[] = "image";
        }
        return $this->genericUpdate($body,$excludeKeys,true,true);
    }

    /**
     * Retorna o produto por parametro
     *
     * @param string $paran
     * @param object $value
     * @return Product
     */
    public function returnByParan($paran,$value)
    {
        $params = ['id','code','token'];
        return $this->transformResult($this->genericReturnParan($paran,$value,$this->fields,$params,$this->innerTables));
    }

    /**
     * Faz paginação dos produtos
     *
     * @param array $body
     * @param string $order
     * @return array
     */
    public function paginate($body,$order = "a.id DESC")
    {

        $completeWhere = $this->mountWhere($body);
        $page = isset($body['page']) ? $body['page'] : 1;
        $forpageApi = isset($body['forpage']) && $body['forpage'] != '' ? \intval($body['forpage']) : $this->forpage;

        $paginateResults = $this->genericPaginate($this->fields,$this->innerTables,$completeWhere,$page,$forpageApi,$order);
        $paginateResults['results'] = $this->transformResults($paginateResults['results']);

        return $paginateResults;

    }
    
    /**
     * Remove o produto do banco
     *
     * @param int $id
     * @return object
     */
    public function delete($id)
    {
        return $this->genericDelete($id);
    }

    /**
     * Atualiza o status do produto
     *
     * @param int $status
     * @param int $id
     * @return object
     */
    public function updateStatus($status,$id)
    {
        return $this->genericUpdateStatus($status,$id);
    }

    /**
     * Retorna o estoque atual do produto
     *
     * @param int $id
     * @return float
     */
    public function getInventory($id)
    {
        $inventory = 0;
        $stmt = $this->PDO->prepare(
            " SELECT inventory FROM {$this->table} WHERE id = :id "
        );
        $stmt->bindValue(':id',$id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        if ($result != null && $result != false){
            $inventory = $result->inventory;
        }
        return $inventory;
    }

    /**
     * Define o estoque do produto
     *
     * @param int $inventory
     * @param int $id
     * @return object
     */
    public function setInventory($inventory,$id)
    {

        try {
            $actual = $this->getInventory($id);
            $difference = $inventory - $actual;
            $stmt = $this->PDO->prepare(
                " UPDATE {$this->table} SET inventory = :inventory WHERE id = :id "
            );
            $stmt->bindValue(':id',$id);
            $stmt->bindValue(':inventory',$inventory);
            $stmt->execute();
            $this->saveHistory("O usuário alterou o estoque para: {$difference}",$id,$difference);
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Salva no histórico do produto
     *
     * @param string $description
     * @param int $id_product
     * @param int $quant
     * @return object
     */
    private function saveHistory($description,$id_product,$quant = null)
    {
        try {
            $stmt = $this->PDO->prepare(
                " INSERT INTO {$this->tableHistory} (id_product,description,id_user,quant) VALUES ".
                ' (:id_product,:description,:id_user,:quant) '
            );
            $stmt->bindValue(':description',$description);
            $stmt->bindValue(':id_product',$id_product);
            $stmt->bindValue(':id_user',\user_logged()->getId());
            $stmt->bindValue(':quant', $quant);
            $stmt->execute();
            return true;
        } catch (Exception $ex) {
            return \throwJsonException($ex->getMessage());
        }
    }

    /**
     * Transforma o array para array de Product
     *
     * @param array $results
     * @param boolean $isApi
     * @return Product[]
     */
    private function transformResults($results,$isApi = false)
    {
        $list = [];
        if (!\is_array($results) || count($results) == 0){
            return $list;
        }
        for ($i = 0; $i < count($results); $i++) {
            $result = (object) $results[$i];
            $obj = $this->transformResult($result,$isApi);
            if ($obj != null){
                $list[] = $obj;
            }
        }
        return $list;
    }

    /**
     * Transforma para OBJETO Product
     *
     * @param object $result
     * @param boolean $isApi
     * @return Product
     */
    private function transformResult($result,$isApi = false)
    {
        
        if ($result == null || $result == false) {
            return null;
        }

        $list_status = \list_status_product();

        $product = new Product();
        $product->setCode($result->code)
        ->setToken($result->token)
        ->setTitle($result->title)
        ->setDescription($result->description)
        ->setImage(!empty($result->image) ? asset("uploads/{$result->image}") : "")
        ->setInventory($result->inventory)
        ->setPriceCost($result->price_cost)
        ->setPriceSeller($result->price_seller)
        ->setStatusDesc($list_status[$result->status])
        ->setPriceMoney($result->price_money)
        ->setSpecification($result->specification)
        ->setId($result->id)
        ->setDataInsert($result->date_insert)
        ->setStatus($result->status);

        if (isset($result->id_category) && !empty($result->id_category)){
            $category = (object)[
                'id' => $result->id_category,
                'slug' => $result->slug_category,
                'title' => $result->title_category
            ];
            $product->setCategory($category);
        }

        return $product;

    }

    /**
     * Monta o SQL STRING QUERY
     *
     * @param array $body
     * @return string
     */
    private function mountWhere($body)
    {
        $completeWhere = '';
        if (!\is_array($body) || count($body) == 0){
            return $completeWhere;
        }

        foreach($body as $k => $v){
            switch($k){
                case 'with_estoque':
                    if ($v != ''){
                        switch($v){
                            case 1:
                                $completeWhere .= " AND ( a.inventory IS NOT NULL AND a.inventory > 0 ) ";
                                break;
                            case 2:
                                $completeWhere .= " AND ( a.inventory IS NULL OR a.inventory <= 0 ) ";
                                break;
                            default:
                                break;
                        }
                    }
                    break;
                case 'code':
                    $completeWhere .= $v != '' ? " AND a.code = '{$v}' " : "";
                    break;
                case 'status':
                    $completeWhere .= $v != '' ? " AND a.status = {$v} " : "";
                    break;
                case 'search':
                    if (!empty($v)){
                        $completeWhere .= " AND ( a.title LIKE '%{$v}%' OR a.description LIKE '%{$v}%' )";
                    }
                    break;
                case 'id_category':
                    $completeWhere .= !empty($v) ? " AND a.id_category = {$v} AND b.id = {$v} " : "";
                    break;
                case 'slug_category':
                    $completeWhere .= !empty($v) ? " AND b.slug LIKE '%{$v}%' " : "";
                    break;
                case 'date_insert':
                    if (!empty($v)){
                        $completeWhere .= " AND DATE(a.date_insert) >= '{$v}' ";
                    }
                    break;
                case 'date_final':
                    if (!empty($v)){
                        $completeWhere .= " AND DATE(a.date_insert) <= '{$v}' ";
                    }
                    break;
                case 'specification':
                    $completeWhere .= !empty($v) ? " AND a.specification LIKE '%{$v}%' " : "";
                    break; 
                default:
                    break;
            }
        }
        return $completeWhere;
    }

}

?>