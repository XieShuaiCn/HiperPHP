<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/12
 * Time: 12:23
 */

namespace Core\Lib;

use Core\Base\Cache;

class CacheLocal extends Cache
{
    private $path = '.';

    public function __construct($path = null)
    {
        if ($path !== null) {
            $this->path = $path;
        }
        var_dump($this->path);
        if (!is_dir($this->path)) {
            if (false == mkdir($this->path)) {
                die('fail to make directory of cache.');
            }
        }
    }

    /**
     * 获取键值
     * @param string $key
     * @return bool|string
     */
    public function getValue($key)
    {
        $file = $this->path . "/" . $key . ".tmp";
        if (is_file($file)) {
            try {
                //$f = fopen($file, 'rb');
                //$data = fread($f, 0xFFFF);
                //fclose($f);
                $data = file_get_contents($file);
                $index = strpos($data, '|');
                if ($index === false) {
                    unlink($file);
                    return false;
                }
                $expire = unserialize(substr($data, 0, $index));
                if ($expire > time()) {
                    $data = unserialize(substr($data, $index + 1));
                    return $data;
                } else {
                    unlink($file);
                    return false;
                }
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * 设置键值
     * @param $key string
     * @param $value mixed
     * @param int $expire
     * @return bool
     */
    public function setValue($key, $value, $expire = 0)
    {
        $file = $this->path . "/" . $key . ".tmp";
        $expire = $expire == 0 ? 0xFFFFFFFF : ((int)$expire + time());
        //var_dump($file);
        try {
            //$f = fopen($file, 'wb');
            $data = serialize($expire) . '|';
            $data .= serialize($value);
            return file_put_contents($file, $data);
            //fwrite($f, $data);
            //fclose($f);
            //return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除键值
     * @param $key string
     * @return bool
     */
    public function deleteValue($key)
    {
        try {
            return
                file_exists($this->path . "/" . $key . ".tmp") ?
                    unlink($this->path . "/" . $key . ".tmp") :
                    true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取所有键名
     * @param string $pattern
     * @return array
     */
    public function getKeysAll($pattern = '*')
    {
        $list = scandir($this->path);
        $ret = [];
        if ($pattern != null && $pattern != '*') {
            $ptn = str_replace('*', '\w*?', $pattern);
            $ptn = '/' . $ptn . '/';
            var_dump($ptn);
            foreach ($list as $l) {
                if (substr($l, -4) == '.tmp' && preg_match($ptn, $l)) {
                    $ret [] = $l;
                }
            }
        } else {
            foreach ($list as $l) {
                if (substr($l, -4) == '.tmp') {
                    $ret [] = $l;
                }
            }
        }
        return $ret;
    }

    /**
     * 是否存在某个键值
     * @param string $key
     * @return bool
     */
    public function existKey($key)
    {
        // TODO: Implement existKey() method.
        return is_file($this->path . '/' . $key . '.tmp');
    }

    /**
     * 获取hash键值
     * @param $name
     * @param $key
     * @return bool|mixed
     */
    public function getHashValue($name, $key)
    {
        return self::getValue($name . '_' . $key);
    }

    /**
     * 设置hash键值
     * @param $name string
     * @param $key string
     * @param $value mixed
     * @param int $expire
     * @return bool
     */
    public function setHashValue($name, $key, $value, $expire = 0)
    {
        if (is_array($value)) {
            return self::setValue($name . '_' . $key, $value, $expire);
        }
        return false;
    }

    /**
     * 删除hash键值
     * @param $name string
     * @param $key string
     * @return bool
     */
    public function deleteHashValue($name, $key)
    {
        return $this->deleteValue($name . '_' . $key);
    }

    /**
     * 获取所有键名
     * @param string $name
     * @return array
     */
    public function getHashKeysAll($name)
    {
        return $this->getKeysAll($name . "_*");
    }

    /**
     * 是否存在某个hash键值
     * @param string $name
     * @param string $key
     * @return bool
     */
    public function existHashKey($name, $key)
    {
        // TODO: Implement existHashKey() method.
        return $this->existKey($name . '_' . $key);
    }

    /**
     * 是否存在某个hash组
     * @param string $name 组名
     * @return bool
     */
    public function existHash($name)
    {
        return count($this->getHashKeysAll($name)) > 0;
    }
}