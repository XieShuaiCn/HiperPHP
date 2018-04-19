<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-18
 * Time: 下午2:08
 */

namespace Core\Module;


class Router
{
    private static $routers;

    public static function init()
    {
        if (!file_exists(APP_ROOT . "/config/router.config.php")) {
            die("missing router configure file");
        }
        self::$routers = include APP_ROOT . "/config/router.config.php";
    }

    /**
     * @param $request Request
     * @param $response Response
     */
    public static function route($request, $response)
    {
        $f = new ControllerFactory();
        //获取请求的模块
        $module = $request->getModule();
        if ($module == null) {
            $conf = self::$routers['default'];
            $c = $f->getInstance(self::$routers['default'][0]);
        }
        $module2 = $module[0] == '/' ? substr($module, 1) : $module;
        $module3 = explode('/', $module2);
        //获取请求的功能
        $control = "Demo";
        if (isset(self::$routers[$module]) && isset(self::$routers[$module][$control])) {
            $conf = self::$routers[$module][$control];
            //var_dump($conf);
            $c = $f->getInstance($conf[0]);
        } elseif (isset(self::$routers[$module2])) {
            var_dump($_SERVER);
        } elseif (isset(self::$routers[$module3[count($module3) - 1]])) {
            var_dump($_SERVER);
        } else {
            //路由到默认位置
        }
        $c->handle($conf[1], $request, $response);
    }
}