<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 17:46
 */

namespace Core\Module;

use Core\Base\Controller;
use Core\Base\Factory;

/**
 * 控制器单例工厂类
 * Class ControllerFactory
 * @package Core\Module
 */
class ControllerFactory extends Factory
{
    /*
     * @var string 类名
     */
    protected $_class_name = "ControllerFactory";
    /**
     * @var array 实例集合
     */
    protected static $instance = [];

    /**
     * 获取控制器实例
     * @param string $name
     * @return Controller
     */
    public function  getInstance($name = "")
    {
        // TODO: Implement getInstance() method.
        if (!isset(self::$instance[$name]) || !(self::$instance[$name] instanceof Controller)) {

            //记录实例
            self::$instance[$name] = $this->createInstance($name);
        }
        return self::$instance[$name];
    }

    /**
     * 创建新的控制器实例
     * @param string $name
     * @return Controller
     */
    public function createInstance($name = "")
    {
        //组合全称
        $full_name = "\\APP\\Controller\\" . $name . "Controller";
        try {
            $ctler = new $full_name();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        return $ctler;
    }
}