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
    protected $_class_name = "Factory";

    /**
     * 获取单例
     * @return mixed
     */
    public abstract function getInstance();

    /**
     * 创建实例
     * @return mixed
     */
    public abstract function createInstance();
}