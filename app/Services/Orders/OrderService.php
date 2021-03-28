<?php

namespace App\Services\Orders;

use App\Models\Consumer;
use App\Models\Order;
use App\Models\User;
use App\Services\Service;
use Exception;

class OrderService extends Service{
    
    private $fields = "a.id,a.code,a.date_insert,a.date_payment,a.date_finish,a.status,a.type_payment,a.parcels,
    b.id as id_consumer,b.token AS token_consumer,b.cpf AS cpf_consumer,b.cnpj AS cnpj_consumer,b.name AS name_consumer,b.phone AS phone_consumer,b.email AS email_consumer,
    c.id AS id_user,c.login AS login_user,c.token AS token_user,c.nome AS nome_user,
    (SELECT SUM(x.price * x.quant) FROM pedido_product AS x WHERE x.id_pedido = a.id AND x.status = 1) AS total,
    (SELECT SUM(z.quant) FROM pedido_product AS z WHERE z.id_pedido = a.id AND z.status = 1) AS qtdProducts";
    private $forpage = 20;
    private $innerTables = [];

    public function __construct(){
        parent::__construct();
        $this->setTable("pedido");
        $this->setPrefix("a");
        $this->innerTables = [
            " LEFT JOIN consumer AS b ON a.id_consumer = b.id ",
            " LEFT JOIN user AS c ON a.id_user = c.id "
        ];
    }

    /**
     * Salva o pedido
     *
     * @param array $body
     * @param boolean $returnId
     * @return object
     */
    public function save($body,$returnId = false)
    {
        return $this->genericeSave($body,['id'],$returnId);
    }

    /**
     * Remove o pedido do banco
     *
     * @param int $id
     * @return object
     */
    public function delete($id)
    {
        return $this->genericDelete($id);
    }

    /**
     * Faz paginação dos pedidos
     *
     * @param array $body
     * @param string $order
     * @param boolean $isApi
     * @return array
     */
    public function paginate($body,$order = "a.id DESC",$isApi = false)
    {
        $completeWhere = $this->mountWhere($body);
        $page = isset($body['page']) ? $body['page'] : 1;
        $forpageApi = isset($body['forpage']) && $body['forpage'] != '' ? \intval($body['forpage']) : $this->forpage;
        
        $paginateResults = $this->genericPaginate($this->fields,$this->innerTables,$completeWhere,$page,$forpageApi,$order);
        $paginateResults['results'] = $this->transformResults($paginateResults['results']);

        return $paginateResults;

    }

    /**
     * Retorna o pedido por parametro
     *
     * @param string $paran
     * @param object $value
     * @param boolean $withProducts
     * @return Order
     */
    public function returnByParan($paran,$value,$withProducts = false)
    {
        $params = ['id','code'];
        return $this->transformResult($this->genericReturnParan($paran,$value,$this->fields,$params,$this->innerTables),$withProducts);
    }

    /**
     * Atualiza o status do pedido
     *
     * @param int $status
     * @param int $id
     * @return object
     */
    public function updateStatus($status,$id)
    {
        try {
            $updateStatus = " status = {$status}";
            if ($status == Order::STATUS_FINISH){
                $date = date("Y-m-d H:i:s");
                $updateStatus .= ", date_finish = '{$date}' ";
            }
            $stmt = $this->PDO->prepare(
                " UPDATE {$this->table} SET {$updateStatus} WHERE id = :id "
            );
            $stmt->bindValue(":id",$id);
            $stmt->execute();
            if ($status == Order::STATUS_BLOCK){
                $productService = new ProductsService();
                $productService->cancelAllProducts($id);
            }
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Transforma o array para array de Users
     *
     * @param array $results
     * @param boolean $withProducts
     * @return Order[]
     */
    private function transformResults($results,$withProducts = false)
    {
        $list = [];
        if (!\is_array($results) || count($results) == 0){
            return $list;
        }
        for ($i = 0; $i < count($results); $i++) {
            $result = (object) $results[$i];
            $obj = $this->transformResult($result,$withProducts);
            if ($obj != null){
                $list[] = $obj;
            }
        }
        return $list;
    }

    /**
     * Transforma para OBJETO Order
     *
     * @param object $result
     * @param boolean $withProducts
     * @return Order
     */
    private function transformResult($result,$withProducts = false)
    {
        
        if ($result == null || $result == false) {
            return null;
        }

        $order = new Order();
        $order->setCode($result->code)
        ->setParcels($result->parcels)
        ->setTypePayment($result->type_payment)
        ->setDatePayment($result->date_payment)
        ->setDateFinish($result->date_finish)
        ->setId($result->id)
        ->setDataInsert($result->date_insert)
        ->setStatus($result->status);

        $list_status = \list_status_order();
        $order->setStatusDesc($list_status[$result->status]);

        if (isset($result->id_consumer) && !empty($result->id_consumer)){
            $consumer = new Consumer();
            $consumer->setToken($result->token_consumer)
            ->setPhone($result->phone_consumer)
            ->setCpf($result->cpf_consumer)
            ->setCnpj($result->cnpj_consumer)
            ->setName($result->name_consumer)
            ->setEmail($result->email_consumer)
            ->setId($result->id);
            $order->setConsumer($consumer);
        }

        if (isset($result->id_user) && !empty($result->id_user)){
            $user = new User();
            $user->setNome($result->nome_user);
            $user->setToken($result->token_user);
            $user->setId($result->id_user);
            $order->setUser($user);
        }

        if ($withProducts){
            $order->setProducts([]);
        }else{
            $order->setProducts([]);
        }

        if (isset($result->qtdProducts)){
            $order->setQtdProducts($result->qtdProducts);
        }

        if (isset($result->total)){
            $order->setTotal($result->total);
        }

        return $order;

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
                case 'code':
                    $completeWhere .= !empty($v) ? " AND a.code = '{$v}' " : "";
                    break;
                case 'id_consumer':
                    $completeWhere .= !empty($v) ? " AND a.id_consumer = {$v} " : "";
                    break;
                case 'id_user':
                    $completeWhere .= !empty($v) ? " AND a.id_user = {$v} " : "";
                    break;
                case 'type_payment':
                    $completeWhere .= !empty($v) ? " AND a.type_payment = {$v} " : "";
                    break;
                case 'status':
                    $completeWhere .= $v != '' ? " AND a.status = {$v} " : "";
                    break;
                case 'exclude_status':
                    $completeWhere .= $v != '' ? " AND a.status != {$v} " : "";
                    break;
                case 'search':
                    if (!empty($v)){
                        $search = $v;
                        if (!preg_match('#[^0-9]#',$search)){
                            $searchHelper = \mask_cpf($v);
                        }else{
                            $searchHelper = $v;
                        }
                        $completeWhere .= " AND ( a.nome LIKE '%{$searchHelper}%' OR a.email LIKE '%{$searchHelper}%' )";
                    }
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
                default :
                    break;
            }
        }
        return $completeWhere;
    }

}

?>