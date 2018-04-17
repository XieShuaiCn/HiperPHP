<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 12:48
 */

namespace Core\Base;

/**
 * 视图基类
 * Class View
 * @package Core\Base
 */
class View extends BaseObject
{
    protected $_class_name = "View";

    /**
     * 显示页面内容
     */
    public function display()
    {
        die("404:" . $this->toString());
    }
}