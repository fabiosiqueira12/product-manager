<?php

namespace App\Services;

use App\Models\Consumer;
use App\Services\Service;
use Exception;

class ConsumerService extends Service{

    private $fields = 'a.id,a.token,a.name,a.phone,a.email,a.date_insert,a.cpf,a.cnpj';
    private $forpage = 20;

    function __construct() {
        parent::__construct();
        $this->setTable("consumer");
        $this->setPrefix("a");
    }

    /**
     * Faz Paginação dos consumidores
     *
     * @param array $body
     * @param boolean $isAPI
     * @return array
     */
    public function paginate($body,$isAPI = false)
    {

        $order = ' a.id DESC ';
        $completeWhere = $this->mountWhere($body);
        $page = isset($body['page']) ? $body['page'] : 1;
        $forpageApi = isset($body['forpage']) && $body['forpage'] != '' ? \intval($body['forpage']) : $this->forpage;

        $innerTables = [];
        $paginateResults = $this->genericPaginate($this->fields,$innerTables,$completeWhere,$page,$forpageApi,$order);
        $paginateResults['results'] = $this->transformResults($paginateResults['results'],$isAPI);
        
        return $paginateResults;

    }

    /**
     * Verifica se já existe o consumidor com o CPF
     *
     * @param string $cpf
     * @param int $id
     * @return boolean
     */
    private function checkCPF($cpf,$id = null){
        if (empty($cpf)){
            return false;
        }
        $where = "";
        if ($id != null){
            $where .= " AND id != {$id} ";
        }
        $stmt = $this->PDO->prepare(
            " SELECT COUNT(a.id) AS totalCount FROM {$this->table} AS a WHERE a.cpf = :cpf {$where} "
        );
        $stmt->bindValue(":cpf",$cpf);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return !empty($result) && $result->totalCount > 0 ? true : false;
    }

    /**
     * Verifica se já existe o consumidor com o CNPJ
     *
     * @param string $cnpj
     * @param int $id
     * @return boolean
     */
    private function checkCNPJ($cnpj,$id = null){
        if (empty($cnpj)){
            return false;
        }
        $where = "";
        if ($id != null){
            $where .= " AND id != {$id} ";
        }
        $stmt = $this->PDO->prepare(
            " SELECT COUNT(a.id) AS totalCount FROM {$this->table} AS a WHERE a.cnpj = :cnpj {$where} "
        );
        $stmt->bindValue(":cnpj",$cnpj);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return !empty($result) && $result->totalCount > 0 ? true : false;
    }

    /**
     * Salva um novo consumidor
     *
     * @param array $body
     * @param boolean $returnId
     * @return object
     */
    public function save($body,$returnId = false)
    {
        if ($this->checkCPF($body['cpf'],null)){
            throw new Exception("O CPF já existe no banco de dados");
        }
        if ($this->checkCNPJ($body['cnpj'],null)){
            throw new Exception("Já existe o CNPJ na base de dados");
        }
        $body['token'] = create_token();
        return $this->genericeSave($body,['id'],$returnId);
    }

    /**
     * Atualiza os dados do consumidor
     *
     * @param array $body
     * @return object
     */
    public function update($body)
    {
        if ($this->checkCPF($body['cpf'],$body['id'])){
            throw new Exception("O CPF já existe no banco de dados");
        }
        if ($this->checkCNPJ($body['cnpj'],$body['id'])){
            throw new Exception("Já existe o CNPJ na base de dados");
        }
        $excludeKeys = ['id','token'];
        if (!isset($body['cpf']) || empty($body['cpf'])){
            $excludeKeys[] = 'cpf';
        }
        if (!isset($body['cnpj']) || empty($body['cnpj'])){
            $excludeKeys[] = 'cnpj';
        }
        return $this->genericUpdate($body,$excludeKeys,true,false);
    }

    /**
     * Remove o consumidor do banco
     *
     * @param int $id_consumer
     * @return object
     */
    public function delete($id_consumer)
    {
        return $this->genericDelete($id_consumer);
    }


    /**
     * Retorna o consumidor por parametro
     *
     * @param string $paran
     * @param object $value
     * @return Consumer
     */
    public function returnByParan($paran,$value)
    {
        $params = ['token','cpf','id','cnpj','email'];
        $innerTables = [];
        return $this->transformResult($this->genericReturnParan($paran,$value,$this->fields,$params,$innerTables));
    }

    /**
     * Transforma o array para array de Consumer
     *
     * @param array $results
     * @param boolean $isApi
     * @return Consumer[]
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
     * Transforma para OBJETO Consumer
     *
     * @param object $result
     * @param boolean $isApi
     * @return Consumer
     */
    private function transformResult($result,$isApi = false)
    {
        
        if ($result == null || $result == false) {
            return null;
        }

        $consumer = new Consumer();
        $consumer->setToken($result->token)
        ->setName($result->name)
        ->setCnpj($result->cnpj)
        ->setCpf($result->cpf)
        ->setEmail($result->email)
        ->setPhone($result->phone)
        ->setId($result->id)
        ->setDataInsert($result->date_insert);

        return $consumer;

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
                case 'type':
                    switch($v){
                        case 1:
                            $completeWhere .= " AND a.cpf IS NOT NULL AND a.cpf != '' ";
                            break;
                        case 2:
                            $completeWhere .= " AND a.cnpj IS NOT NULL AND a.cnpj != '' ";
                            break;
                        default:
                            break;
                    }
                    break;
                case 'search':
                    if (!empty($v)){
                        if (!preg_match('#[^0-9]#',$v)){
                            $searchCpf = \mask_cpf($v);
                            $searchCnpj = \mask_cnpj($v);
                        }
                        $completeWhere .= " AND ( a.name LIKE '%{$v}%' OR a.email LIKE '%{$v}%' OR a.cpf = '{$searchCpf}' OR a.cnpj = '{$searchCnpj}' )";
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