<?php

namespace App\Services\Users;

use App\Services\Service;

class TypeUserService extends Service
{

    private $fields = 'a.id,a.ref,a.order_by,a.title,a.date_insert';

    function __construct() {
        parent::__construct();
        $this->table = "type_user";
    }

    /**
     * Retorna todos os tipos de usuário
     *
     * @return void
     */
    public function getAll()
    {
        $stmt = $this->PDO->prepare(
            " SELECT {$this->fields} FROM {$this->table} AS a ORDER BY order_by ASC LIMIT 10000 "
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Retorna as permiss~ões
     *
     * @return array
     */
    public function getPermissoes()
    {
        $types = [];
        $stmt = $this->PDO->prepare(
            ' SELECT '. $this->fields . ' FROM ' . $this->tableBD.
            ' ORDER BY order_by ASC '
        );
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_OBJ);
        if (is_array($results) && count($results) > 0){
            foreach ($results as $key => $value) {
                $types[$value->id] = $value->title;
            }
        }
        return $types;
    }

    /**
     * Retorna o tipo por id ou ref
     *
     * @param string $paran
     * @param object $value
     * @return object
     */
    public function getByParan($paran,$value)
    {
        $result = null;
        $params = ['ref','id'];
        if (!in_array($paran,$params)){
            return $result;
        }
        $stmt = $this->PDO->prepare(
            ' SELECT ' . $this->fields . ' FROM ' . $this->tableBD.
            ' WHERE ' . $paran . ' = :' . $paran
        );
        $stmt->bindValue(':'.$paran,$value);
        $stmt->execute();
        $v = $stmt->fetch(\PDO::FETCH_OBJ);
        if ($v != null && $v != false){
            $result = $v;
        }
        return $v;
    }


    /**
     * Verifica se já está cadastrado um texto com determinado REF
     *
     * @param string $ref
     * @param int $id
     * @return boolean
     */
    public function checkRef($ref,$id = "")
    {
        $stmt = $this->PDO->prepare("SELECT id FROM " .
            $this->tableBD .
            " WHERE ref = :ref ");
        $stmt->bindValue(':ref', $ref);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_OBJ);
        if ($id != ""){
            if (count($results) > 0){
                $exist = false;
                foreach($results as $key => $value){
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
     * Salva o tipo no banco
     *
     * @param array $body
     * @return object
     */
    public function save($body)
    {

        if ($this->checkRef($body['ref'],'')){
            return \throwJsonException('Já existe esse tipo de usuário');
        }

        try {
            $stmt = $this->PDO->prepare(
                ' INSERT INTO ' . $this->tableBD.
                ' (ref,title,order_by) VALUES (:ref,:title,:order_by) '
            );
            $stmt->bindValue(':ref',$body['ref']);
            $stmt->bindValue(':title',$body['title']);
            $stmt->bindValue(':order_by', isset($body['order_by']) ? $body['order_by'] : 1);
            $stmt->execute();
            return true;
        } catch (\Exception $ex) {
            return \throwJsonException($ex->getMessage());
        }

    }

    /**
     * Edita o texto
     *
     * @param array $body
     * @return object
     */
    public function update($body)
    {
        if ($this->checkRef($body['ref'],$body['id'])){
            return \throwJsonException('Já existe um tipo com essa referência');
        }
        try {
            $stmt = $this->PDO->prepare(
                ' UPDATE ' . $this->tableBD . ' SET '.
                ' ref = :ref,title = :title, order_by = :order_by '.
                ' WHERE id = :id '
            );
            $stmt->bindValue(':ref',$body['ref']);
            $stmt->bindValue(':title',$body['title']);
            $stmt->bindValue(':order_by', isset($body['order_by']) ? $body['order_by'] : 1);
            $stmt->bindValue(':id',$body['id']);
            $stmt->execute();
            return true;
        } catch (\Exception $ex) {
            return \throwJsonException($ex->getMessage());
        }
    }

    /**
     * Remove o tipo do usuário
     *
     * @param int $id
     * @return object
     */
    public function delete($id)
    {
        try {
            $stmt = $this->PDO->prepare(
                'DELETE FROM ' . $this->tableBD.
                ' WHERE id = :id '
            );
            $stmt->bindValue(':id',$id);
            $stmt->execute();
            return true;
        } catch (\Exception $ex) {
            return \throwJsonException($ex->getMessage());
        }
    }

}

?>