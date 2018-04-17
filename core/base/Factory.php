<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 16:52
 */

namespace Core\Base;


abstract class Factory extends BaseObject
{
    protected $_class_name = "Factory";

    public abstract function getInstance();

    public abstract function createInstance();
}