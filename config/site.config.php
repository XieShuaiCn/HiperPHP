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
    "path" => "/var/www/html/",
    "debug" => true,
);
$admin_config = array(
    "url" => $site_config['url'] . "admin/",
    "path" => $site_config['path'] . "admin/",
    "title" => "后台管理",
    "debug" => $site_config['debug'],
);

if ($site_config['debug']) {
    error_reporting(E_ALL);
} else {
    error_reporting(E_ERROR);
}
