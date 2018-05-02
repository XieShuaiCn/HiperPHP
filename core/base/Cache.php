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
    public abstract function getKeysAll();
    public abstract function existKey($key);

    public abstract function getHashValue($name, $key);
    public abstract function setHashValue($name, $key, $value);
    public abstract function deleteHashValue($name, $key);
    public abstract function getHashKeysAll($name);
    public abstract function existHashKey($name, $key);
    public abstract function existHash($name);

}