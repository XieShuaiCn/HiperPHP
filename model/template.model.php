<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2017/8/9
 * Time: 11:13
 */

include_once "functions.php";

/**
 * 通过ID获取模板
 * @param integer $id 模板ID
 * @return string
 */
function template_get_string_by_id($id)
{
    global $site_config;
    $str_template = "";
    $db = get_db_instance();
    $db_row = $db->select_one(array("file"), "template", "`ID`=" . $id);
    if (is_array($db_row)) {
        $file = $site_config['path'] . "template/" . $db_row[0];
        //读取模板内容
        $str_template = file_get_contents($file);
        if (is_bool($str_template) && false == $str_template) {
            $str_template = "";
        }
    }
    return $str_template;
}

/**
 * 通过名获取模板
 * @param string $name 模板名
 * @return string
 */
function template_get_string_by_name($name)
{
    global $site_config;
    $str_template = "";
    $db = get_db_instance();
    $db_row = $db->select_one(array("file"), "template", "`tpName`='" . $name . "'");
    if (is_array($db_row)) {
        $file = $site_config['path'] . "template/" . $db_row[0];
        //读取模板内容
        $str_template = file_get_contents($file);
        if (is_bool($str_template) && false == $str_template) {
            $str_template = "";
        }
    }
    return $str_template;
}