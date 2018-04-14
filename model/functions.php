<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2017/8/7
 * Time: 15:48
 */

include $site_config['path'] . "classes/db_mysqli.class.php";
include $site_config['path'] . "classes/base.func.php";
include $site_config['path'] . "config/db.config.php";
include_once "website.model.php";

//数据库单例
$db_instance = array();
/**
 * 获取数据库实例
 * @return class
 */
function get_db_instance($model = "default")
{
    global $db_cluster_config;
    global $model_db_config;
    global $db_instance;
    //单例模式
    if (isset($model_db_config[$model])) {
        $db_which = $model_db_config[$model];
    } else {
        $db_which = $model_db_config['default'];
    }
    if (!isset($db_instance[$db_which]) || $db_instance[$db_which] == null) {
        $db = new db_mysqli($db_cluster_config[$db_which]["hostname"], $db_cluster_config[$db_which]["user"], $db_cluster_config[$db_which]["password"], $db_cluster_config[$db_which]["dbname"], $db_cluster_config[$db_which]["port"]);
        if ($db != null && is_object($db)) {
            $ret = $db->connect();
            if ($db->is_connected()) {
                $db->set_charset($db_cluster_config[$db_which]["charset"]);
            } else {
                die("cannot connect to database:(" . $ret . ',' . mysqli_connect_errno() . "):" . mysqli_connect_error());
            }
        } else {
            die("cannot get the instance of database");
        }
        $db_instance[$db_which] = $db;
    } else {
        $db = $db_instance[$db_which];
    }
    return $db;
}


/**
 * 获取上一次错误号
 * @return int
 */
function get_last_errno()
{
    global $last_error;
    return isset($last_error) ? $last_error : 0;
}

/**
 * 获取上一次错误字符串
 * @return string
 */
function get_last_error()
{
    global $_error_message;
    return isset($_error_message[get_last_errno]) ? $_error_message[get_last_errno] : "NULL";
}