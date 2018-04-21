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
        $conf = null;
        $module = $request->getModule();
        if ($module == null) {
            $conf = self::$routers['default'];
        }
        $module2 = $module[0] == '/' ? substr($module, 1) : $module;
        $module3 = explode('/', $module2);
        //匹配模块配置，规则不断宽泛
        //绝对路径精准匹配
        if (!is_array($conf)) {
            $conf = self::matchArg(self::$routers, $module);
        }
        //相对路径匹配
        if (!is_array($conf)) {
            $conf = self::matchArg(self::$routers, $module2);
        }
        //文件匹配
        if (!is_array($conf)) {
            $conf = self::matchArg(self::$routers, $module3);
        }
        //默认路由
        if (!is_array($conf)) {
            $conf = self::$routers['default'];
        }
        //获取请求的功能
        $arg = null;
        if (\HiperPHP::config("router_path_info")) {
            $arg = $request->getPathInfo();
        } elseif (\HiperPHP::config("router_url_args")) {
            $arg = $request->getRouterArgs(\HiperPHP::config("router_url_args_name"));
        }
        if (empty($arg)) {
            $arg = "default";
        }
        //匹配参数
        while ($arg[0] == '/') {
            $arg = substr($arg, 1);
        }
        $ctl_arg = [];
        $handle = self::matchArg($conf, $arg, $ctl_arg);
        $request->setRouterArgs($ctl_arg);

        //根据参数调用
        $c = $f->getInstance($handle[0]);
        $c->handle($handle[1], $request, $response);
    }

    private static function matchArg(&$array, $pattern, &$arg = null)
    {
        foreach ($array as $key => &$value) {
            //正则/(\w*)/
            if ($key[0] == '#' && $key[strlen($key) - 1] == '#') {
                if (preg_match($key, $pattern, $arg)) {
                    return $value;
                }
            } else {
                if (strpos($key, ':') !== false) {
                    //解析规则
                    $key_tmp = str_replace(":num", "(\d+)", $key);
                    $key_tmp = str_replace(":word", "(\w+)", $key_tmp);
                    $key_tmp = str_replace(":any", "([^/]+)", $key_tmp);
                    if (preg_match('#^' . $key_tmp . '$#', $pattern, $arg)) {
                        return $value;
                    }
                } else {
                    //直接匹配字符串
                    if ($key == $pattern) {
                        return $value;
                    }
                }
            }
        }
        return null;
    }
}