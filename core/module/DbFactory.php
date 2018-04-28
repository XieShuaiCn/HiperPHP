<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/4/12
 * Time: 12:00
 */

namespace Core\Module;

use Core\Base\Factory;
use Core\Lib\DbMysqli;

/**
 * 数据库单例工厂类
 * Class DbFactory
 * @package Core\Module
 */
class DbFactory extends Factory
{
    /*
     * @var string 类名
     */
    protected $_class_name = "DbFactory";
    /**
     * @var array 实例集合
     */
    private static $instance = [];

    /**
     * 获取数据库实例
     * @param string $model
     * @return DbMysqli
     */
    function getInstance($model = "default")
    {
        // TODO: Implement getInstance() method.
        $db_config = include CONFIG_ROOT . "/db.config.php";
        //单例模式
        if (isset(self::$instance[$model])) {
            $db = self::$instance[$model];
        } else {
            //匹配模型
            if (!isset($db_config[$model]) || !is_array($db_config[$model])) {
                $model = "default";
            }
            //新建mysql实例
            try {
                $db = new DbMysqli($db_config[$model]["hostname"], $db_config[$model]["user"], $db_config[$model]["password"], $db_config[$model]["dbname"], $db_config[$model]["port"]);
                if ($db != null && is_object($db)) {
                    $ret = $db->connect();
                    if ($db->isConnected()) {
                        if (isset($db_config[$model]["charset"])) {
                            $db->setCharset($db_config[$model]["charset"]);
                        }
                    } else {
                        die("Cannot connect to database:(" . $ret . ',' . mysqli_connect_errno() . "):" . mysqli_connect_error());
                    }
                } else {
                    die("Cannot get the instance of database");
                }
            } catch (\Exception $e) {
                die($e->getMessage());
            }
            $db_instance[$model] = $db;
        }
        return $db;
    }

    /**
     * 创建新的数据库实例，未实现
     * @param string $name
     */
    public function createInstance($name = "")
    {
        die("Can not create multiple db instances. Use getInstance instead.");
    }
}