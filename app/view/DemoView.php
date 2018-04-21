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
     * @param $key
     */
    public function setData($value, $key = null)
    {
        if($key == null && is_array($value)) {
            $this->data = array_merge($this->data, $value);
        }
        else{
            $this->data[$key] = $value;
        }
    }

    /**
     * 显示页面
     */
    public function display($response)
    {
        $html = <<<EOF
<html><head><title>{TITLE}</title></head><body>{CONTENT}</body></html>
EOF;
        $html = str_replace("{TITLE}", $this->data['name'], $html);
        $content = isset($this->data['value'])?$this->data['value']."<br>":"";
        $content .= isset($this->data['arg_id'])?"ID：".$this->data['arg_id']."<br>":"";
        $content .= isset($this->data['arg_name'])?"Name：".$this->data['arg_name']."<br>":"";
        $html = str_replace("{CONTENT}", $content, $html);
        $response->setContent($html);
    }
}