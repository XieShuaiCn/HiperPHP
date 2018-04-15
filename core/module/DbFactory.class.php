<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/12
 * Time: 12:00
 */

namespace Core\Module\DbFunc;

use Core\Lib\DbMysqli;

include HIPER_ROOT . "/core/lib/DbMysqli.class.php";

class DbFactory
{
    //数据库单例
    private static $db_instance = [];

    /**
     * 获取数据库实例
     * @param string $model
     * @return DbMysqli
     */
    function getInstance($model = "default")
    {
        $db_config = include CONFIG_ROOT . "db.config.php";
        //单例模式
        if (isset($db_instance[$model])) {
            $db = $this->db_instance[$model];
        } else {
            //匹配模型
            if (!isset($db_config[$model]) || !is_array($db_config[$model])) {
                $model = "default";
            }
            //新建mysql实例
            $db = new DbMysqli($db_config[$model]["hostname"], $db_config[$model]["user"], $db_config[$model]["password"], $db_config[$model]["dbname"], $db_config[$model]["port"]);
            if ($db != null && is_object($db)) {
                $ret = $db->connect();
                if ($db->isConnected()) {
                    if (isset($db_config[$model]["charset"])) {
                        $db->setCharset($db_config[$model]["charset"]);
                    }
                } else {
                    die("cannot connect to database:(" . $ret . ',' . mysqli_connect_errno() . "):" . mysqli_connect_error());
                }
            } else {
                die("cannot get the instance of database");
            }
            $db_instance[$model] = $db;
        }
        return $db;
    }
}