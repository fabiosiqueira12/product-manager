<?php

namespace App\Services;

use App\Services\Service;

class MenuService extends Service 
{

    function __construct() {
        parent::__construct();
        $this->table = "menu";
    }

    /**
     * Retorna os submenus do menu
     *
     * @param int $id
     * @return array
     */
    public function getSubMenus($id)
    {
        $stmt = $this->PDO->prepare(
            "SELECT * FROM {$this->table} AS a ".
            " WHERE a.root = :id ".
            " ORDER by order_by ASC LIMIT 10000 "
        );
        $stmt->bindValue(":id",$id);
		$stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_OBJ);
        return $results;
    }

    public function get()
    {
        dump("SELECT * FROM {$this->table} ORDER BY order_by ASC ");
        $stmt = $this->PDO->prepare(
            "SELECT * FROM {$this->table} ORDER BY id ASC "
        );
		$stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_OBJ);
        return $results;
    }

    public function getByController($controller)
    {
        return $this->genericReturnParan("controller",$controller,"a.*");
    }

    public function getOne($id)
    {
        $stmt = $this->PDO->prepare(
            "SELECT * FROM {$this->table} WHERE id = :id "
        );
        $stmt->bindValue(':id',$id);
		$stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return $result;
    }

    public function save($data)
    {
        return $this->genericeSave($data,['id'],true);
    }

    public function edit($data)
    {
        return $this->genericUpdate($data,['id'],true,false);
    }

    public function delete($ref)
    {
        return $this->genericDelete($ref);
    }

}

?>