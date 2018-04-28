<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/23
 * Time: 23:24
 */

namespace Core\Lib;

use \Core\Base\Cache;

/**
 * Redis缓存类
 * Class CacheRedis
 * @package Core\Lib
 */
class CacheRedis extends Cache
{
    /**
     * @var null|\Redis
     */
    public $_redis = null;
    /**
     * @var string 主机名
     */
    private $host = "127.0.0.1";
    /**
     * @var int 端口
     */
    private $port = 6379;
    /**
     * @var null 密码
     */
    private $password = null;
    /**
     * @var float 超时时间，默认为0.0不限制
     */
    private $timeout = 0.0;
    /**
     * @var null 重试间隔指定时必须为null
     */
    private $reserved = null;
    /**
     * @var int 重试间隔，单位：毫秒
     */
    private $retry_interval = 0;

    /**
     * CacheRedis constructor. null for default.
     * @param null $host
     * @param null $port
     * @param null $password
     * @param null $timeout
     * @param null $retry_time
     */
    public function __construct($host = null, $port = null, $password = null, $timeout = null, $retry_time = null)
    {
        if (!class_exists('\Redis')) {
            die('You have not installed Redis.');
        }
        $this->_redis = new \Redis();
        if ($host !== null) {
            $this->host = $host;
        }
        if ($port !== null) {
            $this->port = $port;
        }
        if ($password !== null) {
            $this->password = $password;
        }
        if ($timeout !== null) {
            $this->timeout = $timeout;
        }
        if ($retry_time !== null) {
            $this->retry_interval = $retry_time;
        }
        $this->connect();
    }

    /**
     * CacheRedis destructor.
     */
    public function __destruct()
    {
        if ($this->isConnected()) {
            $this->_redis->close();
        }
    }

    /**
     * 查看是否连接
     * @return bool
     */
    public function isConnected()
    {
        if ($this->_redis === null) {
            return false;
        }
        try {
            $ping_status = $this->_redis->ping();
            return strpos($ping_status, "PONG") !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 连接Redis
     * @return bool
     */
    public function connect()
    {
        if ($this->isConnected() === false) {
            try {
                $suss = $this->_redis->connect($this->host, $this->port, $this->timeout, $this->reserved, $this->retry_interval);
                //设置了密码则需要验证
                if ($this->password !== null) {
                    $suss &= $this->_redis->auth($this->password);
                }
                //必须开启序列化，否则数组和对象等将不能存储数据
                $suss &= $this->_redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
                return $suss;
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }
        return true;
    }

    /**
     * 获取参数
     * @param int $name
     * @return int
     */
    public function getOption($name)
    {
        return $this->_redis->getOption($name);
    }

    /**
     * 设置参数
     * @param int $name
     * @param int $value
     * @return bool
     */
    public function setOption($name, $value)
    {
        return $this->_redis->setOption($name, $value);
    }

    /**
     * 执行某条命令
     * @param string $cmd
     * @param string $arg
     */
    public function command($cmd, $arg = '')
    {
        $this->_redis->client($cmd, $arg);
    }

    /**
     * 查询键值
     * @param $key
     * @return bool|string
     */
    public function getValue($key)
    {
        return $this->_redis->get($key);
    }

    /**
     * 设置新的键值
     * @param $key string
     * @param $value
     * @param $expire int
     * @return bool
     */
    public function setValue($key, $value, $expire = 0)
    {
        //多条命令，一次提交
        $ret = $this->_redis->multi()
            ->set($key, $value);
        if ($expire !== 0) {
            $ret = $ret->expire($key, $expire);
        }
        $ret = $ret->exec();
        return $ret[0] && ($expire === 0 ?: $ret[1]);
    }

    /**
     * 彻底删除某条键值
     * @param $key
     * @return bool
     */
    public function deleteValue($key)
    {
        return $this->_redis->del($key) > 0;
    }

    /**
     * 设置hash值
     * @param $name string
     * @param $key string
     * @param $value
     * @param 0 $expire int
     * @return bool
     */
    public function setHashValue($name, $key, $value, $expire = 0)
    {
        return $this->_redis->hSet($name, $key, $value);
    }

    /**
     * 查询hash值
     * @param $name
     * @param $key
     * @return string
     */
    public function getHashValue($name, $key)
    {
        return $this->_redis->hGet($name, $key);
    }

    /**
     * 彻底删除hash值
     * @param $name
     * @param $key
     * @return bool
     */
    public function deleteHashValue($name, $key)
    {
        return $this->_redis->hDel($name, $key) > 0;
    }

    /**
     * 获取所有键名
     * @param string $pattern
     */
    public function getKeysAll($pattern = '*')
    {
        return $this->_redis->keys($pattern);
    }

    /**
     * 获取所有hash键名
     * @param string $name hash组名
     */
    public function getHashKeysAll($name)
    {
        return $this->_redis->hKeys($name);
    }

    /**
     * 获取所有的hash值
     * @param string $name hash组名，非键值
     * @return array
     */
    public function getHashAll($name)
    {
        return $this->_redis->hGetAll($name);
    }

    /**
     * 获取上一次错误
     * @return null|string
     */
    public function getLastError()
    {
        return $this->_redis->getLastError();
    }
}