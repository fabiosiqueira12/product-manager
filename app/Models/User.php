<?php

namespace App\Models;

use App\Models\Model;
use JsonSerializable;

class User extends Model implements JsonSerializable
{
    
    private $token;
    private $login;
    private $nome;
    private $telefone;
    private $email;
    private $type;
    private $typeObject;

    public const STATUS_BLOQUEADO = 0;
    public const STATUS_ATIVO = 1;

    public const TYPE_MASTER = 1;
    public const TYPE_DEVELOPER = 4;
    public const TYPE_CLIENT = 5;
    public const TYPE_VENDEDOR = 6;

    public function __construct($json = false) {
        if ($json) $this->set(json_decode($json, true));
    }

    public function set($data) {
        foreach ($data AS $key => $value) {
            if (\is_array($value)){
                $this->{$key} = (object) $value;
            }else{
                $this->{$key} = $value;
            }
        }
    }
    
    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * Retorna o primeiro nome do usuário
     *
     * @return string
     */
    public function getFirstName()
    {
        return explode(" ",$this->nome)[0];
    }

    public function getLogin(){
        return $this->login;
    }

    public function setLogin($login){
        $this->login = $login;
    }

    public function getTelefone(){
        return $this->telefone;
    }

    public function setTelefone($telefone){
        $this->telefone = $telefone;
    }

    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }


    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

    /**
     * check if the type of user is 1 to ADMIN
     */ 
    public function isAdmin()
    {
        if ($this->getType() == self::TYPE_MASTER || $this->getType() == self::TYPE_DEVELOPER){
            return true;
        }
        return false;
    }


    /**
     * Get the value of typeObject
     */ 
    public function getTypeObject()
    {
        return $this->typeObject;
    }

    /**
     * Set the value of typeObject
     *
     * @return  self
     */ 
    public function setTypeObject($typeObject)
    {
        $this->typeObject = $typeObject;
        return $this;
    }

    /**
     * Verifica se o usuário é vendedor
     *
     * @return boolean
     */
    public function isVendedor()
    {
        return $this->getType() == self::TYPE_VENDEDOR ? true : false;
    }

    /**
     * Verifica se o usuário é do tipo cliente
     * @return boolean
     */
    public function isCliente(){
        return $this->getType() == self::TYPE_CLIENT ? true : false;
    }
    
}

?>