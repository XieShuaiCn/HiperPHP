<?php
/**
 * 网站配置文件
 */
return [
    //网站网址
    'url' => 'http://localhost:89/',
    //是否为调试模式
    'debug' => true,
    //服务器时区
    'timezone' => 'Asia/Shanghai',

    //是否启用自动加载功能
    'autoload_enabled' => true,

    //是否启用URL路由功能，不启用时需要手动在访问入口传入控制器实例
    'router_enabled' => true,
    //是否启用URL路由的pathinfo功能，不启用则使用参数匹配
    'router_path_info' => true,
    //是否启用URL路由的url参数传入模型访问信息功能，启用时，必须设置“router_url_args_name”参数
    'router_url_args' => true,
    //URL路由的访问信息获取的参数名
    'router_url_args_name' => 'r',

    //默认处理控制器，推荐设置，在没有处理规则时调用
    'default_controller' => \App\Controller\DemoController::class,

    //cookie有效期，2小时
    'cookie_expire' => 7200,
];