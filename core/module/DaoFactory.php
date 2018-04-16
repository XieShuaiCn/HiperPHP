<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 21:04
 */

namespace Core\Module;


use Core\Base\Factory;

class DaoFactory extends Factory
{
    protected $_class_name = "DaoFactory";

    protected static $instance = [];

    public function  getInstance($name = "")
    {
        // TODO: Implement getInstance() method.
        if (!isset(self::$instance[$name]) || !(self::$instance[$name] instanceof Model)) {
            //组合全称
            $full_name = "\\APP\\Dao\\" . $name . "Dao";
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