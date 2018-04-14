<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2017/8/7
 * Time: 15:48
 */

include $site_config['path']. "classes/db_mysqli.class.php";
include $site_config['path']. "classes/base.func.php";
include "db.config.php";

//数据库单例
$db_instance = null;
/**
 * 获取数据库实例
 * @return class
 */
function get_db_instance()
{
    global $db_config;
    global $db_instance;
    //单例模式
    if($db_instance == null) {
        $db = new db_mysqli($db_config["hostname"], $db_config["user"], $db_config["password"], $db_config["dbname"], $db_config["port"]);
        if ($db != null && is_object($db)) {
            $ret = $db->connect();
            if ($db->is_connected()) {
                $db->set_charset($db_config["charset"]);
            } else {
                die("cannot connect to database:(" . $ret . ',' . mysqli_connect_errno() . "):" . mysqli_connect_error());
            }
        } else {
            die("cannot get the instance of database");
        }
        $db_instance = $db;
    }
    else{
        $db = $db_instance;
    }
    return $db;
}