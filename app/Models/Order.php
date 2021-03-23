<?php

namespace App\Models;

use App\Models\User;
use App\Models\Model;
use JsonSerializable;
use App\Models\Consumer;

class Order extends Model implements JsonSerializable
{

    private $code;
    private $consumer;
    private $user;
    private $date_payment;
    private $date_finish;
    private $statusDesc;
    private $quantProducts;
    private $products;

    public const STATUS_CREATED = 0;
    public const STATUS_PAYED = 1;
    public const STATUS_FINISH = 2;
    public const SATTUS_BLOCK = 3;

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }


    /**
     * Retorna o código do pedido
     * @return string
     */ 
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Define o código do pedido
     * @param string $code
     * @return  self
     */ 
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Retorna o consumidor do pedido
     * @return Consumer
     */ 
    public function getConsumer()
    {
        return $this->consumer;
    }

    /**
     * Define o consumidor do pedido
     * @param Consumer $consumer
     * @return self
     */ 
    public function setConsumer($consumer)
    {
        $this->consumer = $consumer;
        return $this;
    }

    /**
     * Retorna o usuário do pedido
     * @return User
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Define o usuário do pedido
     * @param User $user
     * @return  self
     */ 
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Retorna a data de pagamento do pedido
     * @return mixed
     */ 
    public function getDatePayment()
    {
        return $this->date_payment;
    }

    /**
     * Define a data de pagamento do pedido
     * @param mixed $date_payment
     * @return  self
     */ 
    public function setDatePayment($date_payment)
    {
        $this->date_payment = $date_payment;
        return $this;
    }

    /**
     * Retorna a data de finalização do pedido
     * @return mixed
     */ 
    public function getDateFinish()
    {
        return $this->date_finish;
    }

    /**
     * Define a data de finazização
     * @param mixed $date_finish
     * @return self
     */ 
    public function setDateFinish($date_finish)
    {
        $this->date_finish = $date_finish;
        return $this;
    }

    /**
     * Retorna o status do pedido
     * @return string
     */ 
    public function getStatusDesc()
    {
        return $this->statusDesc;
    }

    /**
     * Define o status do pedido
     * @param string $statusDesc
     * @return self
     */ 
    public function setStatusDesc($statusDesc)
    {
        $this->statusDesc = $statusDesc;
        return $this;
    }

    /**
     * Retorna a quantidade de produtos
     * @return int
     */ 
    public function getQuantProducts()
    {
        return $this->quantProducts;
    }

    /**
     * Define a quantidade de produtos do pedido
     * @param int $quantProducts
     * @return self
     */ 
    public function setQuantProducts($quantProducts)
    {
        $this->quantProducts = $quantProducts;
        return $this;
    }

    /**
     * Retorna os produtos do pedido
     * @return array
     */ 
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Define os produtos pedido
     * @param array $products
     * @return self
     */ 
    public function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }

}
?>