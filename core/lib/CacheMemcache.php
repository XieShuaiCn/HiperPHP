<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/24
 * Time: 11:58
 */

namespace Core\Lib;

use Core\Base\Cache;

class CacheMemcache extends Cache
{
    private $_memcache = null;
    private $host = '127.0.0.1';
    private $port = 11211;
    private $timeout = 1;
    private $weight = 1;

    public function __construct($host = null, $port = null)
    {
        if (class_exists('\Memcached')) {
            $this->_memcache = new \Memcached();
        } elseif (class_exists('\Memcache')) {
            $this->_memcache = new \Memcache();
        } else {
            die("Unsupport memcache.");
        }
        if ($host !== null) {
            $this->host = $host;
        }
        if ($port !== null) {
            $this->port = $port;
        }
        //var_dump($this->_memcache);
        //memcache_connect($this->host, $this->port,$this->timeout);
        $this->connect();
    }

    public function __destruct()
    {
        if ($this->_memcache instanceof \Memcached) {
            $this->_memcache->quit();
        } else {
            $this->_memcache->close();
        }
    }

    public function connect()
    {
        if ($this->_memcache instanceof \Memcached) {
            return
                $this->_memcache->addServer($this->host, $this->port, $this->weight);
        } else {
            return
                $this->_memcache->addServer($this->host, $this->port, true, $this->weight)
                && $this->_memcache->connect($this->host, $this->port);
        }
    }

    public function isConnected()
    {
        return @$this->_memcache->getVersion() !== false;
    }

    /**
     * 获取键值
     * @param string|array $key 键名
     * @return array|string 每个键对应的值
     */
    public function getValue($key)
    {
        return $this->_memcache->get($key);
    }

    /**
     * 设置键值
     * @param string $key 键名
     * @param mixed $value 键值
     * @param int $expire 有效期
     * @return bool
     */
    public function setValue($key, $value, $expire = 0)
    {
        return $this->_memcache->set($key, ($value), $expire);
    }

    /**
     * 删除键值
     * @param string $key 键名
     * @return bool
     */
    public function deleteValue($key)
    {
        return $this->_memcache->delete($key);
    }

    /**
     * 获取hash键值
     * @param string $name 组名
     * @param string $key 键名
     * @return array|string
     */
    public function getHashValue($name, $key)
    {
        return $this->_memcache->get($name . '_' . $key);
    }

    /**
     * 设置hash键值
     * @param string $name 组名
     * @param string $key 键名
     * @param mixed $value 键值
     * @param int $expire 有效期
     * @return bool
     */
    public function setHashValue($name, $key, $value, $expire = 0)
    {
        return $this->_memcache->set($name . '_' . $key, $value, $expire);
    }

    /**
     * 删除hash值
     * @param string $name 组名
     * @param string $key 键名
     * @return bool
     */
    public function deleteHashValue($name, $key)
    {
        return $this->_memcache->delete($name . '_' . $key);
    }

    /**
     * 获取所有键名
     * @param string $pattern 键名
     * @return array
     */
    public function getKeysAll($pattern = '*')
    {
        $list = [];
        $allSlabs = $this->_memcache->getExtendedStats('slabs');
        //var_dump($allSlabs);
        //$items = $this->_memcache->getExtendedStats('items');
        //var_dump($items);
        foreach ($allSlabs as $server => $slabs) {
            foreach ($slabs AS $slabId => $slabMeta) {
                if (is_numeric($slabId)) {//传参非int，会报警告
                    $cdump = $this->_memcache->getExtendedStats('cachedump', (int)$slabId);
                    foreach ($cdump AS $keys => $arrVal) {
                        foreach ($arrVal AS $k => $v) {
                            array_push($list, $k);
                        }
                    }
                }
            }
        }
        if ($pattern != null && $pattern != '*') {
            $ptn = str_replace('*', '\w*?', $pattern);
            $ptn = '/'.$ptn.'/';
            var_dump($ptn);
            $ret = [];
            foreach ($list as $l){
                if(preg_match($ptn, $l)){
                    array_push($ret, $l);
                }
            }
            return $ret;
        }
        return $list;
        /* Valid values are {reset, malloc, maps, cachedump, slabs, items, sizes}.*/
        //return $this->_memcache->getExtendedStats('cachedump', 2);
    }

    /**
     * 获取所有hash键名
     * @param string $name 组名
     * @return array
     */
    public function getHashKeysAll($name)
    {
        return $this->getKeysAll($name.'_*');
    }

    public function getLastError()
    {
        return 0;
    }
}