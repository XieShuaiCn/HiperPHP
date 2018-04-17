<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-15
 * Time: 下午9:35
 */

namespace App\Dao;

use Core\Base\Dao;

/**
 * Class DemoDao
 * @package App\Dao
 */
class DemoDao extends Dao
{
    protected $_class_name = "DemoDao";

    /**
     * @var string 数据表名
     */
    protected $table_name = "Demo";

    /**
     * 通过名字获取一行记录
     * @param $name
     * @param array $fields
     * @return array|null
     */
    public function selectByName($name, $fields = ['*'])
    {
        $where = "`name`='" . $name . "'";
        return $this->db->doSelectOne($fields, $this->table_name, $where);
    }

}