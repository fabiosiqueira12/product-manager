<?php

namespace App\Models;

use App\Models\Model;
use JsonSerializable;

class Product extends Model implements JsonSerializable{

    private $token;
    private $title;
    private $description;
    private $inventory;
    private $category;
    private $statusDesc;
    private $image;
    private $code;
    private $price_cost;
    private $price_seller;
    private $specification;
    private $price_money;

    public const STATUS_ACTIVE = 1;
    public const STATUS_BLOCK = 99;

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

    /**
     * Retorna o token do produto
     * @return string
     */ 
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Define o token do produto
     * @param string $token
     * @return self
     */ 
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Retorna o título do produto
     * @return string
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Define o título do produto
     * @param string $title
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Retorna a descrição do produto
     * @return string
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Define a descrição do produto
     * @param string $description
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Retorna o estoque do produto
     * @return int
     */ 
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * Define o estoque do produto
     * @param int $inventory
     * @return  self
     */ 
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;
        return $this;
    }

    /**
     * Retorna a categoria do produto
     * @return object
     */ 
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Define a categoria do produto
     * @param object $category
     * @return  self
     */ 
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Retorna a descrição do status do produto
     * @return string
     */ 
    public function getStatusDesc()
    {
        return $this->statusDesc;
    }

    /**
     * Define a descrição do status do produto
     * @param string $statusDesc
     * @return  self
     */ 
    public function setStatusDesc($statusDesc)
    {
        $this->statusDesc = $statusDesc;
        return $this;
    }

    /**
     * Retorna a imagem do produto
     * @return string
     */ 
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Define a imagem do produto
     * @param string $image
     * @return  self
     */ 
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Retorna o código do produto
     * @return string
     */ 
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Define o código do produto
     * @param string $code
     * @return  self
     */ 
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Verifica se o produto está ativo
     *
     * @return boolean
     */
    public function isActive(){
        return $this->getStatus() == self::STATUS_ACTIVE;
    }

    /**
     * Verifica se o produto está bloqueado
     *
     * @return boolean
     */
    public function isBlock()
    {
        return $this->getStatus() == self::STATUS_BLOCK;
    }

    /**
     * Retorna o custo o produto
     * @return float
     */ 
    public function getPriceCost()
    {
        return $this->price_cost;
    }

    /**
     * Define o custo do produto
     * @param float $price_cost
     * @return self
     */ 
    public function setPriceCost($price_cost)
    {
        $this->price_cost = $price_cost;
        return $this;
    }

    /**
     * Retorna o valor de venda do produto
     * @return float
     */ 
    public function getPriceSeller()
    {
        return $this->price_seller;
    }

    /**
     * Define o valor de venda do produto
     * @param float $price_seller
     * @return  self
     */ 
    public function setPriceSeller($price_seller)
    {
        $this->price_seller = $price_seller;
        return $this;
    }

    /**
     * Retorna a especificação do produto
     * @return string
     */ 
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * Define a especificação do produto
     * @param string $specification
     * @return  self
     */ 
    public function setSpecification($specification)
    {
        $this->specification = $specification;
        return $this;
    }

    /**
     * Get the value of price_money
     * @return float
     */ 
    public function getPriceMoney()
    {
        return $this->price_money;
    }

    /**
     * Set the value of price_money
     * @param float $price_money
     * @return  self
     */ 
    public function setPriceMoney($price_money)
    {
        $this->price_money = $price_money;
        return $this;
    }

    /**
     * Verifica se o produto tem estoque
     *
     * @return boolean
     */
    public function isInStock()
    {
        return $this->getInventory() > 0;
    }
    
}

?>