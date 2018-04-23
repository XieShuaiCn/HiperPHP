<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-23
 * Time: 下午4:21
 */

namespace Core\Module;

/**
 * Class Cookie
 * @package Core\Module
 */
class Cookie
{
    private $expire = 0;
    private $path = "";
    private $domain = "";
    private $secure = false;
    private $httponly = false;

    public function __construct()
    {
        $expire = \HiperPHP::config('cookie_expire');
        if($expire){
            $this->expire = $expire;
        }
    }

    /**
     * 设置有效时长，单位为秒；或设置到期时间，格式为“Wdy, DD-Mon-YYYY HH:MM:SS GMT”
     * @param $time int|string 有效时长
     * @* @return Cookie $this 返回自身
     */
    public function setExpire($time)
    {
        $this->expire = $time;
        return $this;
    }

    /**
     * 设置cookie有效的路径
     * @param $path string 生效路径
     * @* @return Cookie $this 返回自身
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * 设置生效的主机头
     * @param $domain string 主机头
     * @* @return Cookie $this 返回自身
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * 设置是否必须使用https协议访问
     * @param $secure bool
     * @* @return Cookie $this 返回自身
     */
    public function setNeedSecure($secure)
    {
        $this->secure = $secure;
        return $this;
    }

    /**
     * 设置是否必须使用HTTP访问，不允许其他脚本语言访问，如js等。有的浏览器可能不支持。
     * @param $http bool 是否必须Http访问
     * @return Cookie $this 返回自身
     */
    public function setHttpOnly($http)
    {
        $this->httponly = $http;
        return $this;
    }

    /**
     * 获取指定cookie值，key为null时，获取所有值
     * @param null $key string|null cookie名称
     * @return string|array
     */
    public function getValue($key = null)
    {
        if ($key == null) {
            return $_COOKIE;
        }
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }

    /**
     * 设置cookie值
     * @param $key string cookie名称
     * @param $value string cookie值
     * @return Cookie $this 返回自身
     */
    public function setValue($key, $value = "")
    {
        //非0数字，则视为有效时长，0表示只用浏览器关闭永久有效，其他则视为有效到固定时间的字符串表示。
        if(is_numeric($this->expire) && $this->expire != 0) {
            $time = time() + $this->expire;
        }
        else{
            $time = $this->expire;
        }
        //设置cookie
        setcookie($key, $value, $time, $this->path, $this->domain, $this->secure, $this->httponly);
        return $this;
    }

    /**
     * 删除指定cookie
     * @param string $key cookie名
     * @return Cookie $this 返回自身
     */
    public function delValue($key)
    {
        setcookie($key, "", time()-10000);
        return $this;
    }
}