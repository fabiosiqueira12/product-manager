<?php

namespace App\Models;

use App\Models\Model;
use JsonSerializable;

class Address extends Model implements JsonSerializable
{

    private $cep;
    private $logradouro;
    private $numero;
    private $bairro;
    private $complemento;
    private $cidade;
    private $estado;
    private $referencia;
    private $is_principal;

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

    public function getCep(){
        return $this->cep;
    }

    public function setCep($cep){
        $this->cep = $cep;
    }

    public function getLogradouro(){
        return $this->logradouro;
    }

    public function setLogradouro($logradouro)
    {
        $this->logradouro = $logradouro;
    }

    public function getNumero(){
        return $this->numero;
    }

    public function setNumero($numero){
        $this->numero = $numero;
    }

    public function getBairro(){
        return $this->bairro;
    }

    public function setBairro($bairro){
        $this->bairro = $bairro;
    }

    public function getCidade(){
        return $this->cidade;
    }

    public function setCidade($cidade){
        $this->cidade = $cidade;
    }

    public function getEstado(){
        return $this->estado;
    }

    public function setEstado($estado){
        $this->estado = $estado;
    }

    public function getReferencia(){
        return $this->referencia;
    }

    public function setReferencia($referencia){
        $this->referencia = $referencia;
    }
    
    public function getComplemento(){
        return $this->complemento;
    }

    public function setComplemento($complemento){
        $this->complemento = $complemento;
    }

    public function getIsPrincipal(){
        return $this->is_principal;
    }

    public function setIsPrincipal($is_principal){
        $this->is_principal = $is_principal;
    }

}

?>