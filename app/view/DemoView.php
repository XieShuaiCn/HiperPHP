<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/16
 * Time: 21:06
 */

namespace App\View;

use Core\Base\View;
use Core\Module\CacheFactory;

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
        if ($key == null && is_array($value)) {
            $this->data = array_merge($this->data, $value);
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * 显示页面
     */
    public function display($response)
    {
        $cache_key = $this->_class_name
            . (isset($this->data['arg_id']) ? "_" . $this->data['arg_id'] : "")
            . (isset($this->data['arg_name']) ? "_" . $this->data['arg_name'] : "");
        $cache = CacheFactory::getInstance('core');
        if($html = $cache->getValue($cache_key)){
            $response->setContent($html);
            return;
        }
        $html = <<<EOF
<html><head><title>{TITLE}</title></head><body>{CONTENT}</body></html>
EOF;
        $html = str_replace("{TITLE}", $this->data['name'], $html);
        $content = isset($this->data['value']) ? $this->data['value'] . "<br>" : "";
        $content .= isset($this->data['arg_id']) ? "ID：" . $this->data['arg_id'] . "<br>" : "";
        $content .= isset($this->data['arg_name']) ? "Name：" . $this->data['arg_name'] . "<br>" : "";
        $html = str_replace("{CONTENT}", $content, $html);
        $response->setContent($html);
        $cache->setValue($cache_key, $html);
    }
}