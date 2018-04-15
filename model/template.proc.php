<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2017/8/7
 * Time: 17:25
 */
//PHP正则表达式，使用除字母和反斜杠之外的符号作为首尾，末尾加i表示不区分大小写

include_once "template.model.php";
include_once "image.model.php";
include_once "paper.proc.php";


/**
 * 模板替换字符串
 * @param string $str_template
 * @param string $type
 * @param array $data
 * @param bool $child [=false] 是否为子循环变量
 * @return string
 */
function template_replace($str_template, $type, $data, $child = false)
{
    $key = array_keys($data);
    $value = array_values($data);
    $length = count($data);
    $content = $str_template;
    if (is_bool($child) && $child) {
        $left = "<!--%";
        $right = "%-->";
    } else {
        $left = "<!--{";
        $right = "}-->";
    }
    for ($i = 0; $i < $length; ++$i) {
        //对值为字符串或数字的，进行检查替换
        if (is_string($value[$i]) || is_numeric($value[$i])) {
            $content = str_ireplace($left . $type . "." . $key[$i] . $right, $value[$i], $content);
        }
    }
    return $content;
}

/**
 * 解析模板中的网站配置
 * @param $str_template
 * @param $web
 * @return string
 */
function template_replace_web($str_template, $web)
{
    return template_replace($str_template, "web", $web);
}

/**
 * 解析模板中的图片
 * @param string $str_template
 * @param array $pic
 * @return string
 */
function template_replace_image($str_template, $pic)
{
    return template_replace($str_template, "image", $pic);
}

/**
 * 解析模板中的图片
 * @param string $str_template
 * @param array $pic
 * @return string
 */
function template_replace_image_iter($str_template, $pic)
{
    return template_replace($str_template, "row", $pic, true);
}

/**
 * 解析模板中的论文
 * @param string $str_template
 * @param array $paper
 * @return string
 */
function template_replace_paper($str_template, $paper)
{
    return template_replace($str_template, "paper", $paper);
}

/**
 * 解析模板中的论文
 * @param string $str_template
 * @param array $paper
 * @return string
 */
function template_replace_paper_iter($str_template, $paper)
{
    return template_replace($str_template, "row", $paper, true);
}

/**
 * 获取模板，并解析嵌套模板
 * @param integer $id 模板ID
 * @return string
 */
function template_get_string($temp)
{
    //根据参数类型选择调用
    if (is_numeric($temp)) {
        $str = template_get_string_by_id($temp);
    } else {
        $str = template_get_string_by_name($temp);
    }
    $matches = array();
    //迭代解析嵌套模板
    while ($ret = preg_match_all("/<!--\{template.(\w*)\}-->/i", $str, $matches)) {
        //解析当前层所有模板
        for ($i = 0; $i < $ret; ++$i) {
            $temp = template_get_string($matches[1][$i]);
            $str = str_ireplace($matches[0][$i], "$temp", $str);
        }
    }
    return $str;
}

/**
 * 替换全局变量
 * @return string
 */
function template_replace_global($str_template)
{
    $matches = array();
    $str = $str_template;
    //迭代解析嵌套模板
    while ($ret = preg_match_all("/<!--\{global.([\w_]*)\}-->/i", $str, $matches)) {
        //解析当前层所有模板
        for ($i = 0; $i < $ret; ++$i) {
            //var_dump($matches);
            if (isset($GLOBALS[$matches[1][$i]])) {
                $str = str_ireplace($matches[0][$i], $GLOBALS[$matches[1][$i]], $str_template);
            } else {
                $str = str_ireplace($matches[0][$i], "", $str_template);
            }
        }
    }
    return $str;
}

function template_analyze($str_template, $pic_id = 0)
{
    $matches = array();
    //迭代解析嵌套模板
    if ($ret = preg_match_all("/<!--\[([a-z]+)=\{([\w\:,-]+)\}\]-->([.\w\s=,\.:;@#$%^&_\-+*\/=?|\'\"\{\}\[\]\(\)<>!\\\\《》，。？、“‘：；【】（）￥……—]+?)<!--\[\/\\1\]-->/u", $str_template, $matches)) {
        //遍历匹配项
        for ($_m_i = 0; $_m_i < $ret; ++$_m_i) {
            $temp = trim($matches[3][$_m_i]);
            //处理匹配项
            switch ($matches[1][$_m_i]) {
                //“列表”
                case "loop":
                    $list_temp = "";
                    $maohao_pos = strpos($matches[2][$_m_i], ":");
                    $list_arg = array();
                    if ($maohao_pos) {
                        $list_type = substr($matches[2][$_m_i], 0, $maohao_pos);
                        $list_arg_str = substr($matches[2][$_m_i], $maohao_pos + 1);
                        $list_arg = explode(",", $list_arg_str);
                    } else {
                        $list_type = $matches[2][$_m_i];
                    }
                    switch ($list_type) {
                        default:
                            break;
                    }
                    $str_template = str_replace($matches[0][$_m_i], $list_temp, $str_template);
                    break;
                case "code":
                    //print_r($temp);
                    $code_ret = "";
                    //此方法很危险，慎用！！！
                    str_replace('echo', '', $temp);
                    eval($temp);
                    $str_template = str_replace($matches[0][$_m_i], $code_ret, $str_template);
                    break;
            }
        }
    }
    return $str_template;
}