<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-23
 * Time: 下午4:53
 */

namespace Core\Module;

/**
 * Class Session
 * @package Core\Module
 */
class Session
{
    private $sessionId = null;

    /**
     * Session constructor.
     */
    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $this->sessionId = session_id();
    }

    /**
     * 设置有效期，只对当前用户有效，不修改全局设置
     * @param int|string $time 有效时长，为数字，0为关闭浏览器失效,或到期时间，字符串表示
     * @return Session $this 返回自身
     */
    public function setExpire($time = 0)
    {
        setcookie("PHPSESSID", $this->sessionId, is_numeric($time) && $time != 0 ? (time() + $time) : $time);
        return $this;
    }

    /**
     * 获取指定session值，key为null时，返回所有值
     * @param null $key string|null session名
     * @return string|null session值
     */
    public function getValue($key = null)
    {
        if ($key == null) {
            return $_SESSION;
        }
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * 设置Session值
     * @param string $key session名
     * @param string $value session值
     * @return Session $this 返回自身
     */
    public function setValue($key, $value = "")
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    /**
     * 删除指定session
     * @param string $key session名
     * @return Session $this 返回自身
     */
    public function delValue($key)
    {
        unset($_SESSION[$key]);
        return $this;
    }

    /**
     * 关闭session，保存session信息
     */
    public function close()
    {
        session_write_close();
    }

    /**
     * 清除所有session
     * @return bool
     */
    public function clear()
    {
        return session_destroy();
    }

}