<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/18
 * Time: 20:15
 */

return [
    "default" => ["Demo", "index"],
    "/index" => [
        "default" => ["Demo", "index"],
        "Demo" => ["Demo", "index"],
        "Demo/:any" => ["Demo", "index2"],
        "#^Demo/(\d+)/([^/:]+)$#" => ["Demo", "index3"],
    ]
];