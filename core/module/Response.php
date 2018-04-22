<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/18
 * Time: 20:28
 */

namespace Core\Module;

/**
 * 页面应答类
 * Class Response
 * @package Core\Module
 */
class Response
{
    /**
     * @var bool 是否已经显示完毕
     */
    private $displayed = false;
    /**
     * @var string 页面内容
     */
    private $content = "";
    /**
     * @var int 回应代码，默认为200
     */
    private $responseCode = 200;
    /**
     * @var string 回应网址，主要用于重定向
     */
    private $responseUrl = "";

    /**
     * 设置页面内容
     * @param $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * 显示页面
     */
    public function display()
    {
        if($this->displayed){
            return;
        }
        if ($this->responseCode == 200) {
            echo $this->content;
        }
        elseif ($this->responseCode == 301){
            header('HTTP/1.1 301 Moved Permanently');
            header("Location:" . $this->responseUrl);
        }
        elseif ($this->responseCode == 302 || $this->responseCode == 301) {
            header("Location:" . $this->responseUrl);
        } else {
            \Core\Module\BaseFunc\http_response_code($this->responseCode);
        }
        $this->displayed = true;
    }

    /**
     * 设置回应代码
     * @param int $code
     */
    public function setResponseCode($code = 200)
    {
        $this->responseCode = $code;
    }

    /**
     * 设置重定向
     * @param $url string 网址
     * @param bool $movedTemp 是否临时重定向
     */
    public function redirect($url, $movedTemp = true)
    {
        if (!empty($url) && is_string($url)) {
            $this->responseUrl = $url;
            $this->responseCode = $movedTemp ? 302 : 301;
        }
    }
}