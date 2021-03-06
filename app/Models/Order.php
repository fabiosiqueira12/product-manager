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
    private $type_payment;
    private $parcels;
    private $total;
    private $qtdProducts;

    public const STATUS_CREATED = 0;
    public const STATUS_PAYED = 1;
    public const STATUS_FINISH = 2;
    public const STATUS_BLOCK = 3;

    public const TYPE_MONEY = 0;
    public const TYPE_DEPOSIT = 1;
    public const TYPE_CHECK = 2;
    public const TYPE_CREDIT_CARD = 3;

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

    /**
     * Verifica se o pedido está criado
     *
     * @return boolean
     */
    public function isCreated()
    {
        return $this->getStatus() == self::STATUS_CREATED;
    }

    /**
     * Verifica se o pedido está pago
     *
     * @return boolean
     */
    public function isPayed()
    {
        return $this->getStatus() == self::STATUS_PAYED;
    }

    /**
     * Verifica se o pedido está finalizado
     *
     * @return boolean
     */
    public function isFinish()
    {
        return $this->getStatus() == self::STATUS_FINISH;
    }

    /**
     * Verifica se o pedido está cancelado
     *
     * @return boolean
     */
    public function isBlock()
    {
        return $this->getStatus() == self::STATUS_BLOCK;
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

    /**
     * Retorna o tipo de pagamento
     * @return int
     */ 
    public function getTypePayment()
    {
        return $this->type_payment;
    }

    /**
     * Define o tipo de pagamento do pedido
     * @param int $type_payment
     * @return  self
     */ 
    public function setTypePayment($type_payment)
    {
        $this->type_payment = $type_payment;
        return $this;
    }

    /**
     * Retorna o número de parcelas
     * @return int
     */ 
    public function getParcels()
    {
        return $this->parcels;
    }

    /**
     * Define o número de parcelas
     * @param int $parcels
     * @return  self
     */ 
    public function setParcels($parcels)
    {
        $this->parcels = $parcels;
        return $this;
    }

    /**
     * Retorna a descrição do tipo de pagamento
     *
     * @return string 
     */
    public function getTypePaymentDesc()
    {
        $list_types = \list_type_payment();
        return $list_types[$this->getTypePayment()];
    }
    
    /**
     * Retorna o valor total do pedido
     * @return float
     */ 
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Define o valor total somando os preços do pedido 
     * @param float $total
     * @return  self
     */ 
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * Retorna a quantidade de produtos
     * @return int
     */ 
    public function getQtdProducts()
    {
        return $this->qtdProducts;
    }

    /**
     * Define a quantidade de produtos
     * @param int qtdProducts
     * @return self
     */ 
    public function setQtdProducts($qtdProducts)
    {
        $this->qtdProducts = $qtdProducts;
        return $this;
    }
}
?>