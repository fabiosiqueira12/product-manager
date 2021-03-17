<?php

namespace App\Services;

use Exception;
use App\Core\Connection;

abstract class Service
{

    protected $table;
    protected $prefix;

    /**
     * PDO variable
     *
     * @var \PDO
     */
    protected $PDO;

    function __construct()
	{
        $connection = new Connection();
        $this->PDO = $connection->returnConnection();
    }

    /**
     * Define o nome da tabela
     *
     * @param string $table
     * @return void
     */
    protected function setTable($table){
        $this->table = $table;
    }

    /**
     * Define o prefixo usado para nomear a tabela
     *
     * @param string $prefix
     * @return void
     */
    protected function setPrefix($prefix){
        $this->prefix = $prefix;
    }

    /**
     * Retorna o último resultado da tabela inserido
     *
     * @param string $fields
     * @return object
     */
    protected function genericLastInsert($fields){
        $this->checkTableAndPrefix();
        $stmt = $this->PDO->prepare(
            " SELECT {$fields} FROM {$this->table} AS {$this->prefix} ORDER BY a.id DESC LIMIT 1 "
        );
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return empty($result) ? null : $result;
    }

    /**
     * Salva os dados no banco
     *
     * @param array $body
     * @param array $excludeKeys
     * @param boolean $returnId
     * @return object
     */
    protected function genericeSave($body,$excludeKeys = [],$returnId = true){

        $this->checkTableAndPrefix();

        if (!\is_array($body) || count($body) == 0){
            throw new Exception("Nenhum parametro foi informado");
        }

        $sql = "INSERT INTO {$this->table} (";
        $complete = "";
        foreach($body as $k => $v){
            if (count($excludeKeys) == 0 || !in_array($k,$excludeKeys)){
                if ($complete != ""){
                    $complete .= ", ";
                }
                $complete .= $k;
            }
        }
    
        $sql .= $complete;
        $sql .= ')';
        $sql .= ' VALUES (';
        $complete = "";
        foreach($body as $k => $v){
            if (count($excludeKeys) == 0 || !in_array($k,$excludeKeys)){
                if ($complete != ""){
                    $complete .= ", ";
                }
                $complete .= ":{$k}";
            }
        }
        
        $sql .= $complete;
        $sql .= ')';
    
        try {
            $stmt = $this->PDO->prepare(
                $sql
            );
            foreach($body as $k => $v){
                if (count($excludeKeys) == 0 || !in_array($k,$excludeKeys)){
                    $stmt->bindValue(":{$k}", \validate_data($k,$v));
                }
            }
            $stmt->execute();
            if ($returnId){
                return \intval($this->PDO->lastInsertId());
            }
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Update generico para salvar no banco
     *
     * @param array $body
     * @param array $excludeKeys
     * @param boolean $withWhere
     * @param boolean $returnId
     * @return object
     */
    protected function genericUpdate($body,$excludeKeys = [],$withWhere = true,$returnId = true)
    {

        $this->checkTableAndPrefix();

        if (!\is_array($body) || count($body) == 0){
            throw new Exception("Nenhum parametro foi informado");
        }

        if ($withWhere && (!isset($body['id']) || empty($body['id']))){
            throw new Exception("O ID não foi informado");
        }

        $sql = "UPDATE {$this->table} SET ";
        $complete = '';
        foreach($body as $k => $v){
            if (count($excludeKeys) == 0 || !in_array($k,$excludeKeys)){
                if ($complete != ''){
                    $complete .= ",";
                }
                $complete .= "{$k} = :{$k}";
            }
        }
        $sql .= $complete;
        if ($withWhere){
            $sql .= ' WHERE id = :id';
        }

        try {
            $stmt = $this->PDO->prepare(
                $sql
            );
            if ($withWhere){
                $stmt->bindValue(":id",$body['id']);
            }
            foreach($body as $k => $v){
                if (count($excludeKeys) == 0 || !in_array($k,$excludeKeys)){
                    $stmt->bindValue(":{$k}",\validate_data($k,$v));
                }
            }
            $stmt->execute();
            if ($returnId){
                return \intval($body['id']);
            }
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
    
    /**
     * Retorna um dado por parametro e valor
     *
     * @param string $paran
     * @param object $value
     * @param string $fields
     * @param array $paramsPermitted
     * @return object
     */
    protected function genericReturnParan($paran,$value,$fields,$paramsPermitted = [],$innerTables = [])
    {

        $this->checkTableAndPrefix();

        if (count($paramsPermitted) > 0 && !in_array($paran,$paramsPermitted)){
            return null;
        }

        $inner = "";
        if (count($innerTables) > 0){
            $inner .= implode(" ",$innerTables);
        }

        $stmt = $this->PDO->prepare(
            " SELECT {$fields} FROM {$this->table} AS {$this->prefix} {$inner} WHERE {$this->prefix}.{$paran} = :{$paran} "
        );
        $stmt->bindValue(":{$paran}",$value);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return empty($result) ? null : $result;

    }

    /**
     * Remove do banco por ID
     *
     * @param int $id
     * @return object
     */
    protected function genericDelete($id)
    {
        $this->checkTableAndPrefix();
        try {
            $stmt = $this->PDO->prepare(
                " DELETE FROM {$this->table} WHERE id = :id "
            );
            $stmt->bindValue(":id",$id);
            $stmt->execute();
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Atualiza o status
     *
     * @param int $status
     * @param int $id
     * @return object
     */
    public function genericUpdateStatus($status,$id)
    {
        $this->checkTableAndPrefix();
        try {
            $stmt = $this->PDO->prepare(
                " UPDATE {$this->table} SET status = :status WHERE id = :id "
            );
            $stmt->bindValue(":status",$status);
            $stmt->bindValue(":id",$id);
            $stmt->execute();
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Retorna paginação dos resultados
     *
     * @param string $fields
     * @param array $innerTables
     * @param string $where
     * @param int $page
     * @param int $forpage
     * @param string $order
     * @return array
     */
    protected function genericPaginate($fields,$innerTables = [],$where = "",$page = 1,$forpage = 20,$order = "a.id DESC")
    {
        $forpageApi = $forpage;
        $multiple = ($forpageApi * $page);
        $startPage = $multiple - $forpageApi;

        $inner = "";
        if (count($innerTables) > 0){
            $inner .= implode(" ",$innerTables);
        }

        $stmt = $this->PDO->prepare(
            " SELECT {$fields} FROM {$this->table} AS a {$inner} WHERE a.id != 0 {$where} ".
            " ORDER BY {$order} LIMIT {$startPage},{$forpageApi}"
        );
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_OBJ);
        
        $stmt = $this->PDO->prepare(
            " SELECT COUNT(a.id) as totalCount FROM {$this->table} AS a {$inner} WHERE a.id != 0 {$where} "
        );
        $stmt->execute();
        $resultTotal = $stmt->fetch(\PDO::FETCH_OBJ);
        
        $totalCount = 0;
        if ($resultTotal != false && $resultTotal != null){
            $totalCount = $resultTotal->totalCount;
        }
        
        $numPages = $totalCount > 0 ? \ceil($totalCount / $forpageApi) : 0;

        $paginate = \mount_paginate($numPages,$page,5);

        return [
            'totalPages' => $numPages,
            'results' => $results,
            'totalCount' => $totalCount,
            'fim' => $paginate['fim'],
            'inicio' => $paginate['inicio'],
            'actual' => $page
        ];
    }

    /**
     * Verifica se a tabela e o prefixo está defindo
     *
     * @return void
     */
    private function checkTableAndPrefix()
    {
        if (empty($this->table)){
            throw new Exception("A tabela não foi informada");
        }
        if (empty($this->prefix)){
            $this->setPrefix("a");
        }
    }
    
}

?>