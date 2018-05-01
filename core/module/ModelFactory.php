<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 16:26
 */

namespace Core\Module;

use Core\Base\Factory;
use Core\Base\Model;

/**
 * 模型单例工厂类
 * Class ModelFactory
 * @package Core\Module
 */
class ModelFactory extends Factory
{
    /*
     * @var string 类名
     */
    protected static $class_name = "ModelFactory";
    protected $_class_name = "ModelFactory";
    /**
     * @var array 实例集合
     */
    protected static $instance = [];

    /**
     * 获取模型实例
     * @param string $name 模型名称
     * @return Model 返回模型
     */
    public static function getInstance($name = "")
    {
        // TODO: Implement getInstance() method.
        if (!isset(self::$instance[$name]) || !(self::$instance[$name] instanceof Model)) {
            //记录实例
            self::$instance[$name] = self::createInstance($name);
        }
        return self::$instance[$name];
    }

    /**
     * 创建新的模型实例，
     * @param string $name
     * @return Model
     */
    public static function createInstance($name = "")
    {
        //组合全称
        $full_name = "\\APP\\Model\\" . $name . "Model";
        try {
            $model = new $full_name();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        return $model;
    }
}