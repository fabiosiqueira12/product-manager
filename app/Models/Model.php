<?php

namespace App\Models;

abstract class Model
{
    protected $id;
    protected $status;
    protected $dataInsert;
    protected $dataUpdate;

    public function getId(){
        return $this->id;   
    }
    public function setId($id){
        $this->id = $id;
    }
    public function getDataInsert()
    {
        return $this->dataInsert;
    }
    public function setDataInsert($dataInsert)
    {
        $this->dataInsert = $dataInsert;

        return $this;
    }
    public function getDataUpdate()
    {
        return $this->dataUpdate;
    }
    public function setDataUpdate($dataUpdate)
    {
        $this->dataUpdate = $dataUpdate;

        return $this;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }

}

?>