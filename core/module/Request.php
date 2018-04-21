<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/18
 * Time: 20:28
 */

namespace Core\Module;


class Request
{
    private $method = "GET";
    private $language = "zh-CN";
    private $module = null;
    private $router_args = [];

    public function __construct()
    {
        $this->language = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $index = strrpos($_SERVER['SCRIPT_NAME'], '.');
        if ($index !== false) {
            $this->module = substr($_SERVER['SCRIPT_NAME'], 0, $index);
        }
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getScriptFile($full_path = false)
    {
        return $full_path ? $_SERVER['SCRIPT_FILENAME'] : $_SERVER['SCRIPT_NAME'];
    }

    public function getPostArgs($key = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    public function getUrlArgs($key = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    public function setRouterArgs($key, $value = "")
    {
        if (is_array($key)) {
            $this->router_args = array_merge($this->router_args, $key);
        } else {
            $this->router_args[$key] = $value;
        }
    }

    public function getRouterArgs($key = null)
    {
        if ($key === null) {
            return $this->router_args;
        }
        return isset($this->router_args[$key]) ? $this->router_args[$key] : null;
    }

    public function getPathInfo()
    {
        return isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
    }

    public function isHttps()
    {
        return isset($_SERVER['REQUEST_SCHEME']) ? ($_SERVER['REQUEST_SCHEME'] == "https") : false;
    }
}