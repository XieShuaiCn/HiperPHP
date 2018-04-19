<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2017/8/17
 * Time: 15:15
 */

return [
    'url' => 'http://localhost:89/',
    'debug' => true,
    'timezone' => 'Asia/Shanghai',
    'autoload_enabled' => true,
    'router_enabled' => true,
    'router_path_info' => true,
    'router_url_args' => true,
    'router_url_args_name' => 'r',
    'default_controller' => \App\Controller\DemoController::class,
];