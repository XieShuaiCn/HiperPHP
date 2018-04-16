<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 16:23
 */

namespace Core\Base;

class BaseObject
{
    protected $_class_name = "BaseObject";

    public static function create()
    {
        return new BaseObject();
    }

    public function toString()
    {
        return "< " . $this->_class_name . " >";
    }
}