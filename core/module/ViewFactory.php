<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 21:13
 */

namespace Core\Module;

use Core\Base\Factory;
use Core\Base\View;

/**
 * 视图单例工厂类
 * Class ViewFactory
 * @package Core\Module
 */
class ViewFactory extends Factory
{
    /*
     * @var string 类名
     */
    protected static $class_name = "ViewFactory";
    protected $_class_name = "ViewFactory";
    /**
     * @var array 实例集合
     */
    protected static $instance = [];

    /**
     * 获取视图实例
     * @param string $name 模型名称
     * @return View 返回模型
     */
    public static function getInstance($name = "")
    {
        // TODO: Implement getInstance() method.
        if (!isset(self::$instance[$name]) || !(self::$instance[$name] instanceof View)) {
            //记录实例
            self::$instance[$name] = self::createInstance($name);
        }
        return self::$instance[$name];
    }

    /**
     * 创建新的视图实例，
     * @param string $name
     * @return View
     */
    public static function createInstance($name = "")
    {
        //组合全称
        $full_name = "\\APP\\View\\" . $name . "View";
        try {
            $view = new $full_name();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        return $view;
    }
}