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

    public function getArgs($key = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ?: null;
    }

    public function getPathInfo()
    {
        return isset($_SERVER['PATH_INFO']) ?: null;
    }

    public function isHttps()
    {
        return isset($_SERVER['REQUEST_SCHEME']) ? ($_SERVER['REQUEST_SCHEME'] == "https") : false;
    }
}