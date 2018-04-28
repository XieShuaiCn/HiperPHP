<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-26
 * Time: 上午11:12
 */

namespace Core\Base;


abstract class Cache extends BaseObject
{
    public abstract function getValue($key);
    public abstract function setValue($key, $value);
    public abstract function deleteValue($key);
}