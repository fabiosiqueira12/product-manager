<?php

namespace App\Services;

class DadosService extends Service
{

    private $fieldsAPI = 'id,title,description,keywords,email,telefone,date_insert,facebook,instagram,whatsapp,twitter,token_access';

    function __construct() {
        parent::__construct();
        $this->table = "data_system";
    }

    /**
     * Verifica se o token válido
     *
     * @param string $token_access
     * @return boolean
     */
    public function checkToken($token_access)
    {
        $stmt = $this->PDO->prepare(
            " SELECT COUNT(id) AS total FROM {$this->table} WHERE token_access = :token_access "
        );
        $stmt->bindValue(':token_access',$token_access);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return $result != null && $result != false && $result->total > 0 ? true : false;
    }
    
    /**
     * Retorna os dados
     *
     * @param boolean $isApi
     * @return void
     */
    public function getDados($isApi = false)
    {
        return $this->genericLastInsert($this->fieldsAPI);
    }

    public function edit($body)
    {
        $excludeKeys = [
            'id'  
        ];
        return $this->genericUpdate($body,$excludeKeys);
    }

    public function save($body)
    {
        $excludeKeys = [
            'id'  
        ];
        return $this->genericeSave($body,$excludeKeys);
    }
    
}

?>