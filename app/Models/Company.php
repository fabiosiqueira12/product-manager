<?php

namespace App\Models;

use App\Models\Model;
use JsonSerializable;
 
class Company extends Model implements JsonSerializable
{
    
    private $token;
    private $titulo;
    private $urlamigavel;
    private $type;
    private $with_config;

    public const STATUS_ATIVO = 1;
    public const STATUS_BLOQUEADO = 0;

    public const TYPE_COMERCIO = 0;
    public const TYPE_DELIVERY = 1;

    public const ID_VALIANT = 36;
    public const ID_VESTAK = 61;
    public const ID_ACQUALARA = 12;
    public const ID_PASSO_A_PASSO = 94;

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

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

    /**
     * Retorna o token da empresa
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;   
    }

    /**
     * Define o token
     *
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Define o título
     *
     * @param string $titulo
     * @return void
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    /**
     * Retorna o título
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Retorna a urlamigavel
     *
     * @return string
     */
    public function getUrlAmigavel()
    {
        return $this->urlamigavel;
    }

    /**
     * Define a urlamigavel
     *
     * @param string $urlamigavel
     * @return void
     */
    public function setUrlAmigavel($urlamigavel)
    {
        $this->urlamigavel = $urlamigavel;
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return void
     */ 
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Verifica se a empresa é do comercio
     *
     * @return boolean
     */
    public function isComercio(){
        return $this->getType() == self::TYPE_COMERCIO ? true : false;
    }
    
    /**
     * Verifica se o a empresa é delivery
     *
     * @return boolean
     */
    public function isDelivery(){
        return $this->getType() == self::TYPE_DELIVERY ? true : false;
    }


    /**
     * Get the value of with_config
     * @return boolean
     */ 
    public function getWithConfig()
    {
        return $this->with_config;
    }

    /**
     * Set the value of with_config
     * @param boolean $with_config
     * @return void
     */ 
    public function setWithConfig($with_config)
    {
        $this->with_config = $with_config;
    }

    /**
     * Verifica se é Passo a Passo
     *
     * @return boolean
     */
    public function isPassoAPasso()
    {
        return $this->getId() == self::ID_PASSO_A_PASSO ? true : false;   
    }

}
?>