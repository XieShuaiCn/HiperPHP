<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 21:06
 */

namespace App\View;

use Core\Base\View;

/**
 * Class DemoView
 * @package App\View
 */
class DemoView extends View
{
    protected $_class_name = "DemoView";

    /**
     * @var array 数据
     */
    private $data = [];

    /**
     * 设置数据
     * @param $value
     */
    public function setData($value)
    {
        $this->data = $value;
    }

    /**
     * 显示页面
     */
    public function display($response)
    {
        $content = <<<EOF
<html><head><title>{TITLE}</title></head><body>{CONTENT}</body></html>
EOF;
        $html = str_replace("{TITLE}", $this->data['name'], $content);
        $html = str_replace("{CONTENT}", $this->data['value'], $html);
        $response->setContent($html);
    }
}