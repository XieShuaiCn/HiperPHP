<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 19:34
 */

namespace Core\Module;


class AutoLoad
{
    protected static $_core_class = [
        //核心类
        //"HiperPHP" => "HiperPHP.php",
        //核心库文件
        "CacheLocal" => "lib/CacheLocal.php",
        "DbMysql" => "lib/DbMysql.php",
        "DBMysqlExt" => "lib/DbMysqlExt.php",
        "DBMysqli" => "lib/DbMysqli.php",
        "Page" => "lib/Page.php",
        //基础文件
        "BaseObject" => "base/BaseObject.php",
        "Controller" => "base/Controller.php",
        "Dao" => "base/Dao.php",
        "Factory" => "base/Factory.php",
        "Model" => "base/Model.php",
        "View" => "base/View.php",
        //模块功能
        //"AutoLoad" => "module/AuotLoad.php",
        "ControllerFactory" => "module/ControllerFactory.php",
        "DbFactory" => "module/DbFactory.php",
        "ModelFactory" => "module/ModelFactory.php",
    ];

    public static function init()
    {
        if (false === spl_autoload_register(['Core\Module\AutoLoad', 'loadClass'])) {
            die("AutoLoad initialize fail.");
        }
    }

    public static function loadClass($class)
    {
        //解析类名，提取到类名和命名空间
        $class_name_index = strrpos($class, "\\");
        if ($class_name_index === false) {
            $class_name = $class;
            $class_namespace = "";
        } else {
            $class_name = substr($class, $class_name_index + 1);
            $class_namespace = substr($class, 0, $class_name_index);
        }
        //判断是否为核心类
        if (isset(self::$_core_class[$class_name])) {
            $file = CORE_ROOT . "/" . self::$_core_class[$class_name];
        } else {
            //优先根据命名控件解析
            if (strlen($class_namespace) > 0) {
                $class_path = str_replace("\\", "/", strtolower($class_namespace));
                $file = HIPER_ROOT . "/" . $class_path . "/" . $class_name . ".php";
            } else {
                //根据类名解析路径
                if (substr($class_name, -10) == "Controller") {
                    $file = APP_ROOT . "/controller/" . $class_name . ".php";
                } elseif (substr($class_name, -3) == "Dao") {
                    $file = APP_ROOT . "/dao/" . $class_name . ".php";
                } elseif (substr($class_name, -5) == "Model") {
                    $file = APP_ROOT . "/model/" . $class_name . ".php";
                } elseif (substr($class_name, -4) == "View") {
                    $file = APP_ROOT . "/view/" . $class_name . ".php";
                } else {
                    $file = APP_ROOT . $class_name . ".php";
                }
            }
        }
        //引入文件
        include $file;
    }
}