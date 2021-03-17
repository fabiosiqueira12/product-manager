<?php

namespace App\Core;

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;

class CacheControl{

    private static $instance;

    /**
     * Tempo default como um dia em segundos
     */
    public const TIME_DAY = 86400;

    /**
     * Retorna o último objeto criado
     *
     * @return CacheControl
     */
    public static function getInstance()
    {
        if(self::$instance === null){
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Cache Control
     *
     * @var object
     */
    protected $cache;
    
    private function __construct()
    {
        CacheManager::setDefaultConfig(new ConfigurationOption([
            'path' => __DIR__ . '/../../temp/cache/'
        ]));
        $this->cache = CacheManager::getInstance('files');
    }

    private function __clone(){

    }

    private function __wakeup(){

    }
    
    /**
     * Verifica se está expirado o cache
     * @param string $key
     * @return boolean
     */
    public function checkExpired($key)
    {
        $cached = $this->cache->getItem($key);
        return $cached->isExpired();
    }

    /**
     * Remove a informação salva em cache
     *
     * @param string $key
     * @return void
     */
    public function delete($key)
    {
        $this->cache->deleteItem($key);
    }

    /**
     * Retorna o conteúdo salvo em cache a partir da key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        $cached = $this->cache->getItem($key);
        if (!is_null($cached->get()) && !$cached->isExpired()) { 
            return $cached->get();
        }
        return null;
    }

    /**
     * Salva o conteudo em cache
     *
     * @param mixed $data
     * @param string $key
     * @param int $time
     * @return void
     */
    public function save($data,$key,$time = self::TIME_DAY)
    {
        $cached = $this->cache->getItem($key);
        $cached->set($data)->expiresAfter($time);
        $this->cache->save($cached);
    }

}

?>