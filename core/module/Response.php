<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/18
 * Time: 20:28
 */

namespace Core\Module;


class Response
{
    private $content = "";

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function display()
    {
        echo $this->content;
    }
}