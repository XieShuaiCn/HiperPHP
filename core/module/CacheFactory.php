<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-26
 * Time: 上午11:08
 */

namespace Core\Module;


use \Core\Base\Cache;
use \Core\Base\Factory;
use \Core\Lib\CacheLocal;
use \Core\Lib\CacheMemcache;
use \Core\Lib\CacheRedis;

class CacheFactory extends Factory
{
    /*
     * @var string 类名
     */
    protected $_class_name = "ControllerFactory";
    /**
     * @var array 实例集合
     */
    protected static $instance = [];

    /**
     * 获取控制器实例
     * @param string $model
     * @return Cache
     */
    public function getInstance($model = "default")
    {
        // TODO: Implement getInstance() method.
        $db_config = include CONFIG_ROOT . "/cache.config.php";
        //单例模式
        if (isset(self::$instance[$model])) {
            $cache = self::$instance[$model];
        } else {
            //匹配模型
            if (!isset($db_config[$model]) || !is_array($db_config[$model])) {
                $model = "default";
            }
            //新建实例
            try {
                switch ($db_config[$model]['type']) {
                    case 'redis':
                        $host = isset($db_config[$model]['host']) ? $db_config[$model]['host'] : null;
                        $port = isset($db_config[$model]['port']) ? $db_config[$model]['port'] : null;
                        $password = isset($db_config[$model]['password']) ? $db_config[$model]['password'] : null;
                        $timeout = isset($db_config[$model]['timeout']) ? $db_config[$model]['timeout'] : null;
                        $retry_time = isset($db_config[$model]['retry_time']) ? $db_config[$model]['retry_time'] : null;
                        $cache = new CacheRedis($host, $port, $password, $timeout, $retry_time);
                        break;
                    case 'memcache':
                        $host = isset($db_config[$model]['host']) ? $db_config[$model]['host'] : null;
                        $port = isset($db_config[$model]['port']) ? $db_config[$model]['port'] : null;
                        $cache = new CacheMemcache($host, $port);
                        break;
                    case 'files':
                        $path = isset($db_config[$model]['path']) ? $db_config[$model]['path'] : CACHE_ROOT;
                        $cache = new CacheLocal($path);
                        break;
                    default:
                        die('Unsupported cache type');
                }
            } catch (\Exception $e) {
                die('Cannot get the instance of cache');
            }
            self::$instance[$model] = $cache;
        }
        return $cache;
    }

    /**
     * @return void
     */
    public function createInstance()
    {
        // TODO: Implement createInstance() method.
        die('Can not create multiple cache instances. Use getInstance instead.');
    }
}