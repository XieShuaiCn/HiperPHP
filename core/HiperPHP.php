<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/12
 * Time: 11:54
 */

define("HIPERPHP", 1);

//定义程序目录
define("HIPER_ROOT", dirname(__DIR__));
define("CORE_ROOT", HIPER_ROOT . "/core");
define("APP_ROOT", HIPER_ROOT . "/app");
define("WEB_ROOT", HIPER_ROOT . "/web");
define("CACHE_ROOT", HIPER_ROOT . "/cache");
define("CONFIG_ROOT", HIPER_ROOT . "/config");

include CORE_ROOT . "/module/AutoLoad.php";

use \Core\Module\AutoLoad;
use \Core\Module\Router;
use \Core\Module\Request;
use \Core\Module\Response;
use \Core\Base\Controller;

/**
 * HiperPHP核心类
 * Class HiperPHP
 */
class HiperPHP
{
    //错误号
    private static $_error_no = 0;

    private static $_config = ['timezone' => 'Asia/Shanghai', 'debug' => true];

    /**
     * 初始化
     */
    public static function init()
    {
        //加载配置文件
        $conf = include CONFIG_ROOT . "/site.config.php";
        if ($conf === false){
            die("Unable to load site configure");
        }
        self::$_config = array_merge(self::$_config, $conf);
        //时区设置
        if (self::config('timezone')) {
            date_default_timezone_set(self::$_config['timezone']);
        }

        //是否调试模式
        if (self::config('debug')) {
            ini_set('display_errors', true);
            error_reporting(E_ALL);
        }
        /*else{
            //跟随服务器设置
        }*/

        //初始化Autoload
        if (self::config('autoload_enabled')) {
            AutoLoad::init();
        }
        //初始化路由
        if (self::config('router_enabled')) {
            Router::init();
        }
    }

    /**
     * 运行处理请求
     * @param $controller Controller 处理请求的控制器
     */
    public static function run($controller = null)
    {
        $request = new Request();
        $response = new Response();
        //处理请求
        try {
            if (self::config('router_enabled')) {
                //路由到指定控制器
                Router::route($request, $response);
            } elseif ($controller instanceof Controller) {
                //控制器处理
                $controller->handle(null, $request, $response);
            } elseif (self::config('default_controller')) {
                //默认控制器处理
                $cls_nm = self::config('default_controller');
                $controller = new $cls_nm();
                $controller->handle(null, $request, $response);
            } else {
                //无法处理
                die('do not handle the request');
            }
        } catch (Exception $e) {
            if (self::$_config['debug']) {
                die($e->getMessage());
            }
            die("Inner error");
        }
        $response->display();
    }

    public static function config($key)
    {
        return isset(self::$_config[$key]) ?: null;
    }

    /**
     * 设置错误号
     */
    public static function setLastErrorNo($n = 0)
    {
        if (is_numeric($n)) {
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