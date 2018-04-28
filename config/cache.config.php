<?php
/**
 * redis配置文件
 */

return [
    //没有匹配项时的默认选择，一般为file，如果有redis或memcache，请自行修改
    "default" => [
        //类型：文件
        'type' => 'files',
        //文件缓存路径
        "path" => CACHE_ROOT,
    ],
    //核心模块使用的缓存，如果没有redis可以注释此配置
    "core" => [
        //类型：redis
        'type'=>'redis',
        //主机，默认为本机，可以注释此行
        "hostname" => "127.0.0.1",
        //端口，一般默认为6379
        "port" => 6379,
    ]
];