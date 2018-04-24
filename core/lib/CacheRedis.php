<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/23
 * Time: 23:24
 */

namespace Core\Lib;

/**
 * Redis缓存类
 * Class CacheRedis
 * @package Core\Lib
 */
class CacheRedis
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
     */
    public function __construct($host = null, $port = null, $password = null, $timeout = null, $retry_time = null)
    {
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
    }

    /**
     * CacheRedis destructor.
     */
    public function __destruct()
    {
        $this->_redis->close();
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
            $suss = $this->_redis->connect($this->host, $this->port, $this->timeout, $this->reserved, $this->retry_interval);
            //设置了密码则需要验证
            if ($this->password !== null) {
                $suss &= $this->_redis->auth($this->password);
            }
            //必须开启序列化，否则数组和对象等将不能存储数据
            $suss &= $this->_redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            return $suss;
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
        $available = $this->_redis->get($key . '_available');
        if ($available) {
            return $this->_redis->get($key);
        } else {
            return $available;
        }
    }

    /**
     * 设置新的键值
     * @param $key
     * @param $value
     * @return bool
     */
    public function setValue($key, $value)
    {
        //多条命令，一次提交
        $ret = $this->_redis->multi()
            ->set($key, $value)
            ->set($key . '_available', true);
        $ret = $ret->exec();
        return $ret[0] && $ret[1];
    }

    /**
     * 使某个键值失效，可用于lazy-load模式
     * @param $key
     * @return bool
     */
    public function disableValue($key)
    {
        return $this->_redis->set($key . '_available', false);
    }

    /**
     * 彻底删除某条键值
     * @param $key
     * @return bool
     */
    public function deleteValue($key)
    {
        return $this->_redis->del($key . '_available', $key) == 2;
    }

    /**
     * 设置hash值
     * @param $name
     * @param $key
     * @param $value
     * @return bool
     */
    public function setHashValue($name, $key, $value)
    {
        return $this->_redis->hMSet($name, [$key . '_available' => true, $key => $value]);
    }

    /**
     * 查询hash值
     * @param $name
     * @param $key
     * @return string
     */
    public function getHashValue($name, $key)
    {
        $ret = $this->_redis->hGet($name, $key . '_available');
        if ($ret) {
            return $this->_redis->hGet($name, $key);
        } else {
            return $ret;
        }
    }

    /**
     * 使某个键值失效，可用于lazy-load模式
     * @param $name string hash组名
     * @param $key string hash键名
     * @return bool
     */
    public function disableHashValue($name, $key)
    {
        return $this->_redis->hset($name, $key . '_available', false);
    }

    /**
     * 彻底删除hash值
     * @param $name
     * @param $key
     * @return bool
     */
    public function deleteHashValue($name, $key)
    {
        return $this->_redis->hDel($name, $key . '_available', $key) == 2;
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