<?php

namespace App\Models;

use App\Models\Model;
use JsonSerializable;

class Consumer extends Model implements JsonSerializable{

    private $cnpj;
    private $cpf;
    private $token;
    private $name;
    private $phone;
    private $email;
    
    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

    /**
     * Retorna o CNPJ do consumidor
     * @return string
     */ 
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * Define o CNPJ do consumidor
     * @param string $cnpj
     * @return  self
     */ 
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    /**
     * Retorna o CPF do consumidor
     * @return string
     */ 
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * Define o CPF do consumidor
     * @param string $cpf
     * @return  self
     */ 
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
        return $this;
    }

    /**
     * Retorna o token do consumidor
     * @return string
     */ 
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Define o token do consumidor
     * @param string $token
     * @return  self
     */ 
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Retorna o nome da empresa ou pessoa
     * @return string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Define o nome da empresa ou pessoa
     * @param string $name
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retorna o telefone do consumidor
     * @return string
     */ 
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Define o telefone do consumidor
     * @param string $phone
     * @return  self
     */ 
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Retorna o e-mail do consumidor
     * @return string
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Define o e-mail do consumidor
     * @param strings $email
     * @return self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Verifica se o consumidor é empresa
     * @return boolean
     */
    public function isCompany()
    {
        return !empty($this->getCnpj());
    }

    /**
     * Verifica se o consumidor é pessoa física
     *
     * @return boolean
     */
    public function isPerson()
    {
        return !empty($this->getCpf());
    }

}

?>