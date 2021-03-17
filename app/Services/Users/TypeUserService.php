<?php

namespace App\Services\Users;

use App\Services\Service;
use Exception;

class TypeUserService extends Service
{

    private $fields = 'a.id,a.ref,a.order_by,a.title,a.date_insert';

    function __construct() {
        parent::__construct();
        $this->setTable("type_user");
        $this->setPrefix("a");
    }

    /**
     * Retorna todos os tipos de usuário
     *
     * @return void
     */
    public function getAll()
    {
        $stmt = $this->PDO->prepare(
            " SELECT {$this->fields} FROM {$this->table} AS {$this->prefix} ORDER BY a.order_by ASC LIMIT 10000 "
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
            " SELECT {$this->fields} FROM {$this->table} AS {$this->prefix} ORDER BY {$this->prefix}.order_by ASC LIMIT 10000"
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
        $params = ['ref','id'];
        return $this->genericReturnParan($paran,$value,$this->fields,$params);
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
        $stmt = $this->PDO->prepare(
            "SELECT id FROM {$this->table} WHERE ref = :ref "
        );
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
            throw new Exception('Já existe esse tipo de usuário');
        }
        return $this->genericeSave($body,['id'],false);
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
            throw new Exception('Já existe esse tipo de usuário');
        }
        return $this->genericUpdate($body,['id'],true,false);
    }

    /**
     * Remove o tipo do usuário
     *
     * @param int $id
     * @return object
     */
    public function delete($id)
    {
        return $this->genericDelete($id);
    }

}

?>