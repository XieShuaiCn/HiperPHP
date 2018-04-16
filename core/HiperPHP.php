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
define("CORE_ROOT", HIPER_ROOT . "/core");
define("APP_ROOT", HIPER_ROOT . "/app");
define("WEB_ROOT", HIPER_ROOT . "/web");
define("CACHE_ROOT", HIPER_ROOT . "/cache");
define("CONFIG_ROOT", HIPER_ROOT . "/config");

include CORE_ROOT . "/module/AutoLoad.php";

use Core\Module\AutoLoad;

class HiperPHP
{
    //错误号
    private static $_error_no = 0;

    public static function init()
    {
        //基础设置
        date_default_timezone_set("Asia/Shanghai");
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        //初始化Autoload
        AutoLoad::init();
    }

    /**
     * 设置错误号
     */
    public static function setLastErrorNo($n = 0)
    {
        if(is_numeric($n)) {
            self::$_error_no = $n;
        }
    }

    /**
     * 获取上一次错误号
     * @return int
     */
    public static function getLastErrorNo()
    {
        return self::$_error_no;
    }

    /**
     * 获取上一次错误字符串
     * @return string
     */
    public static function getLastError()
    {
        global $error_messages;
        return isset($error_messages[self::$_error_no]) ? $error_messages[self::$_error_no] : "NULL";
    }
}