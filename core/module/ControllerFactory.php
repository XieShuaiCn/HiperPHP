<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 17:46
 */

namespace Core\Module;

use Core\Base\Factory;

class ControllerFactory extends Factory
{
    protected $_class_name = "ControllerFactory";

    protected static $instance = [];

    public function  getInstance($name = "")
    {
        // TODO: Implement getInstance() method.
        if (!isset(self::$instance[$name]) || !(self::$instance[$name] instanceof Model)) {
            //组合全称
            $full_name = "\\APP\\Controller\\" . $name . "Controller";
            try {
                $model = new $full_name();
            } catch (\Exception $e) {
                die($e->getMessage());
            }
            //记录实例
            self::$instance[$name] = $model;
        }
        return self::$instance[$name];
    }
}