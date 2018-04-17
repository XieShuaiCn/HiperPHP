<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 16:23
 */

namespace Core\Base;

/**
 * 基础对象类
 * Class BaseObject
 * @package Core\Base
 */
class BaseObject
{
    protected $_class_name = "BaseObject";

    /**
     * 对象转字符串
     * @return string
     */
    public function toString()
    {
        return "< " . $this->_class_name . " >";
    }
}