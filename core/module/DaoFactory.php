<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 21:04
 */

namespace Core\Module;

use Core\Base\Dao;
use Core\Base\Factory;

/**
 * 数据访问对象单例工厂类
 * Class DaoFactory
 * @package Core\Module
 */
class DaoFactory extends Factory
{
    /**
     * @var string 类名
     */
    protected static $class_name = "DaoFactory";
    protected $_class_name = "DaoFactory";

    /**
     * @var array 实例集合
     */
    protected static $instance = [];

    /**
     * 获取实例
     * @param string $name
     * @return Dao
     */
    public static function getInstance($name = "")
    {
        // TODO: Implement getInstance() method.
        if (!isset(self::$instance[$name]) || !(self::$instance[$name] instanceof Dao)) {
            //记录实例
            self::$instance[$name] = self::createInstance($name);
        }
        return self::$instance[$name];
    }

    /**
     * 创建新的数据访问对象实例，
     * @param string $name
     * @return Dao
     */
    public static function createInstance($name = "")
    {
        //组合全称
        $full_name = "\\APP\\Dao\\" . $name . "Dao";
        try {
            $dao = new $full_name();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        return $dao;
    }
}