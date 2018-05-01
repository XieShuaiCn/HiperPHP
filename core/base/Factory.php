<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 16:52
 */

namespace Core\Base;

/**
 * 工厂类
 * Class Factory
 * @package Core\Base
 */
abstract class Factory extends BaseObject
{
    protected static $class_name = "Factory";
    protected $_class_name = "Factory";

    /**
     * 获取单例
     * @return mixed
     */
    public static function getInstance()
    {
        die("Have not override the '" . self::$class_name . "::getInstance' method.");
    }

    /**
     * 创建实例
     * @return mixed
     */
    public static function createInstance()
    {
        die("Have not override the '" . self::$class_name . "::createInstance' method.");
    }
}