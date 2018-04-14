<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2017/8/7
 * Time: 15:48
 */

/**
 * 数据库集群配置
 */
$db_cluster_config = array(
    "main" => array(
        "type" => "mysql",
        "hostname" => "localhost",
        "user" => "hiperphp",
        "password" => "hiperphp",
        "dbname" => "HiperPHP",
        "port" => "3306",
        "charset" => "utf8"
    ),
    "log" => array(
        "type" => "mysql",
        "hostname" => "localhost",
        "user" => "hiperphp",
        "password" => "hiperphp",
        "dbname" => "HiperPHP_log",
        "port" => "3306",
        "charset" => "utf8"
    )
);

/**
 * 数据模型和数据库对应关系
 */
$model_db_config = array(
    "default" => "main",
    "logs" => "log"
);
