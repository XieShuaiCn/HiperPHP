<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/12
 * Time: 11:54
 */

define("HIPERPHP", 1);

//定义程序目录
define("HIPER_ROOT", dirname(dirname(__FILE__)));
define("APP_ROOT", HIPER_ROOT . "/app");
define("WEB_ROOT", HIPER_ROOT . "/web");
define("CACHE_ROOT", HIPER_ROOT . "/cache");
define("CONFIG_ROOT", HIPER_ROOT . "/config");

class HiperPHP
{
    //错误号
    private $_error_no = 0;

    public function init()
    {
        date_default_timezone_set("Asia/Shanghai");
        ini_set('display_errors', true);
        error_reporting(E_ALL);
    }

    /**
     * 获取上一次错误号
     * @return int
     */
    public function get_last_errno()
    {
        return $this->_error_no;
    }

    /**
     * 获取上一次错误字符串
     * @return string
     */
    public function get_last_error()
    {
        global $error_messages;
        return isset($error_messages[$this->_error_no]) ? $error_messages[$this->_error_no] : "NULL";
    }
}