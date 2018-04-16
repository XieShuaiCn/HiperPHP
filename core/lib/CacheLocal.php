<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/12
 * Time: 12:23
 */

namespace Core\Lib;

class CacheLocal
{
    public function __construct()
    {
        if (!is_dir(CACHE_ROOT)) {
            mkdir(CACHE_ROOT);
        }
    }

    public function getValue($key)
    {
        $file = CACHE_ROOT . "/" . $key . ".tmp";
        if (is_file($file)) {
            $data = file_get_contents($file);
            $index = strpos($data, "|");
            //var_dump($data, $index);
            if ($index === false) {
                unlink($file);
                return false;
            }
            $expire = substr($data, 0, $index);
            if ($expire > time()) {
                return substr($data, $index + 1);
            } else {
                unlink($file);
                return false;
            }
        }
        return false;
    }

    public function setValue($key, $value, $expire = 0xFFFFFFFF)
    {
        $file = CACHE_ROOT . "/" . $key . ".tmp";
        //时间戳|数据
        $data = ($expire == 0xFFFFFFFF ? $expire : (string)((int)$expire + time()));
        $data .= "|";
        $data .= $value;
        return 0 < file_put_contents($file, $data);
    }

    public function getArray($key)
    {
        $value = self::getValue($key);
        if ($value === false) {
            return false;
        }
        $arr = json_decode($value);
        return $arr;
    }

    public function setArray($key, $value, $expire = 0xFFFFFFFF)
    {
        if (is_array($value)) {
            return self::setValue($key, json_encode($value), $expire);
        }
        return false;
    }
}