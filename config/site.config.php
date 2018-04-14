<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2017/8/17
 * Time: 15:15
 */

date_default_timezone_set("Asia/Shanghai");

$site_config = array(
    "url" => "/",
    "path" => "/var/www/",
    "title" => "故宫名画记",
    "debug" => true,
);
$admin_config = array(
    "url" => "/admin/",
    "parh" => "/var/www/admin/",
    "title" => "故宫名画记后台管理",
    "debug" => true,
);

if($site_config['debug']) {
    error_reporting(E_ALL);
}
else{
    error_reporting(E_ERROR);
}