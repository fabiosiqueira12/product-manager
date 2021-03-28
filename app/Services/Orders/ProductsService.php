<?php

namespace App\Services\Orders;

use App\Services\Service;
use Exception;

class ProductsService extends Service{

    public function __construct(){
        parent::__construct();
        $this->setTable("pedido_product");
        $this->setPrefix("a");
    }

    /**
     * Salva o produto no banco
     *
     * @param array $body
     * @return object
     */
    public function save($body){
        return $this->genericeSave($body,['id'],true);
    }

    /**
     * Atualiza quantidade do produto de um pedido
     *
     * @param int $quant
     * @param int $id_product
     * @param int $id_order
     * @return object
     */
    public function updateQuant($quant,$id_product,$id_order)
    {
        try {
            $stmt = $this->PDO->prepare(
                " UPDATE {$this->table} SET quant = quant + :quant WHERE id_product = :id_product AND id_pedido = :id_order "
            );
            $stmt->bindValue(":quant",$quant);
            $stmt->bindValue(":id_order",$id_order);
            $stmt->bindValue(":id_product",$id_product);
            $stmt->execute();
            if ($quant < 0){
                $this->returnInventory($id_product,$quant * -1);
            }else{
                $this->removeInventory($id_product,$quant);
            }
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Cancela todos os produtos do pedido
     *
     * @param int $id_order
     * @return void
     */
    public function cancelAllProducts($id_order)
    {
        $stmt = $this->PDO->prepare(
            " SELECT a.* FROM {$this->table} AS {$this->prefix} WHERE a.id_pedido = :id_order AND a.status = 1 "
        );
        $stmt->bindValue(":id_order",$id_order);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_OBJ);
        if (is_array($results) && count($results) > 0){
            foreach($results as $v){
                $this->remove($id_order,$v->id_product);
            }
        }
    }

    /**
     * Salva os produtos ao pedido
     *
     * @param array $list
     * @param int $id_order
     * @return object
     */
    public function saveMany($list,$id_order){
        if (empty($list)){
            throw new Exception("Os produtos não foram informados");
        }
        $insert = "";
        foreach($list as $v){
            if ($insert != ''){
                $insert .= ",";
            }
            $insert .= "({$id_order},{$v['id_product']},{$v['price']},{$v['quant']})";
            $this->removeInventory($v['id_product'],$v['quant']);
        }
        try {
            $stmt = $this->PDO->prepare(
                " INSERT INTO {$this->table} (id_pedido,id_product,price,quant) VALUES {$insert} "
            );
            $stmt->execute();
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * REmove o produto do pedido
     *
     * @param int $id
     * @return object
     */
    public function delete($id)
    {
        return $this->genericDelete($id);
    }

    /**
     * Remove o produto do pedido
     *
     * @param int $id_product
     * @param int $id_order
     * @return object
     */
    public function remove($id_order,$id_product)
    {
        try {
            $stmt = $this->PDO->prepare(
                " UPDATE {$this->table} SET status = 0 WHERE id_pedido = :id_order AND id_product = :id_product "
            );
            $stmt->bindValue(":id_order",$id_order);
            $stmt->bindValue(":id_product",$id_product);
            $stmt->execute();
            $this->returnInventory($id_product,$this->getQuant($id_product,$id_order));
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
    
    /**
     * Retorna a quantidade do produto adicionada ao pedido
     *
     * @param int $id_product
     * @param int $id_order
     * @return int
     */
    private function getQuant($id_product,$id_order)
    {
        $stmt = $this->PDO->prepare(
            " SELECT a.quant FROM {$this->table} AS {$this->prefix} WHERE a.id_product = :id_product AND a.id_pedido = :id_order "
        );
        $stmt->bindValue(":id_order",$id_order);
        $stmt->bindValue(":id_product",$id_product);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return !empty($result) ? $result->quant : 0;
    }

    /**
     * Remove a quantidade do produto do estoque
     *
     * @param int $id_product
     * @param int $quant
     * @return void
     */
    private function removeInventory($id_product,$quant)
    {
        try {
            $stmt = $this->PDO->prepare(
                " UPDATE product SET inventory = inventory - :quant WHERE id = :id_product "
            );
            $stmt->bindValue(":quant",$quant);
            $stmt->bindValue(":id_product",$id_product);
            $stmt->execute();
        } catch (Exception $ex) {
            
        }
    }

    /**
     * Retorna a quantidade do produto ao estoque
     *
     * @param int $id_product
     * @param int $quant
     * @return void
     */
    private function returnInventory($id_product,$quant)
    {
        try {
            $stmt = $this->PDO->prepare(
                " UPDATE product SET inventory = inventory + :quant WHERE id = :id_product "
            );
            $stmt->bindValue(":quant",$quant);
            $stmt->bindValue(":id_product",$id_product);
            $stmt->execute();
        } catch (Exception $ex) {
            
        }
    }

}

?>