<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/18
 * Time: 20:28
 */

namespace Core\Module;

/**
 * 用户请求类
 * Class Request
 * @package Core\Module
 */
class Request
{
    /**
     * @var string 请求类型
     */
    private $method = "GET";
    /**
     * @var array|string 请求的语言
     */
    private $language = "zh-CN";
    /**
     * @var null|string 请求的模块，访问入口文件名
     */
    private $module = null;
    /**
     * @var array 路由获取的参数
     */
    private $router_args = [];

    private $_session = null;
    private $_cookie = null;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->language = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $index = strrpos($_SERVER['SCRIPT_NAME'], '.');
        if ($index !== false) {
            $this->module = substr($_SERVER['SCRIPT_NAME'], 0, $index);
        }
    }

    /**
     * @return null|string 获取访问文件名，不含php扩展名
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * 获取访问的脚本文件
     * @param bool $full_path 是否返回全路径
     * @return string
     */
    public function getScriptFile($full_path = false)
    {
        return $full_path ? $_SERVER['SCRIPT_FILENAME'] : $_SERVER['SCRIPT_NAME'];
    }

    /**
     * 获取POST参数
     * @param null $key 参数为null时，返回所有参数
     * @return string|array
     */
    public function getPostArgs($key = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    /**
     * 获取URL参数
     * @param null $key 参数为null时，返回所有值
     * @return string|array
     */
    public function getUrlArgs($key = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    /**
     * 获取访问Server资源的参数
     * @param null $key string 参数为null时，返回所有参数
     * @return string|array
     */
    public function getServerArgs($key=null)
    {
        if($key == null){
            return $_SERVER;
        }
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    /**
     * 获取session管理器
     * @return Session
     */
    public function getSession()
    {
        if($this->_session == null){
            $this->_session = new Session();
        }
        return $this->_session;
    }

    /**
     * 获取指定session值
     * @param string $key
     * @return null|string
     */
    public function getSessionArgs($key)
    {
        return $this->getSession()->getValue($key);
    }

    /**
     * 设置session值
     * @param string $key
     * @param string $value
     */
    public function setSessionArg($key, $value)
    {
        $this->getSession()->setValue($key, $value);
    }

    /**
     * 获取cookie管理器
     * @return Cookie|null
     */
    public function getCookie()
    {
        if($this->_cookie == null){
            $this->_cookie = new Cookie();
        }
        return $this->_cookie;
    }

    /**
     * 获取指定cookie值
     * @param string $key
     * @return null|string
     */
    public function getCookieArgs($key)
    {
        return $this->getCookie()->getValue($key);
    }

    /**
     * 设置cookie值
     * @param $key
     * @param string $value
     */
    public function setCookieArg($key, $value="")
    {
        $this->getCookie()->setValue($key, $value);
    }

    /**
     * 设置路由参数
     * @param $key string|array 参数名，或参数数组
     * @param string $value 参数值
     */
    public function setRouterArgs($key, $value = "")
    {
        if (is_array($key)) {
            $this->router_args = array_merge($this->router_args, $key);
        } else {
            $this->router_args[$key] = $value;
        }
    }

    /**
     * 获取路由参数
     * @param null $key 参数为null时，返回所有参数
     * @return array|mixed|null
     */
    public function getRouterArgs($key = null)
    {
        if ($key === null) {
            return $this->router_args;
        }
        return isset($this->router_args[$key]) ? $this->router_args[$key] : null;
    }

    /**
     * 获取Pathinfo信息
     * @return string|null
     */
    public function getPathInfo()
    {
        return isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
    }

    /**
     * 是否为Https请求
     * @return bool
     */
    public function isHttps()
    {
        return isset($_SERVER['REQUEST_SCHEME']) ? ($_SERVER['REQUEST_SCHEME'] == "https") : false;
    }
}