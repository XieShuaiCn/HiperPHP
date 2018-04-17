<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-15
 * Time: 下午8:30
 */

namespace Core\Base;

use Core\Module\DbFactory;

/**
 * 数据访问对象基类
 * Class Dao
 * @package Core\Base
 */
class Dao extends BaseObject
{
    protected $_class_name = "Dao";
    /**
     * @var string 数据表名称
     */
    protected $table_name = null;
    /**
     * @var string 数据表主键
     */
    protected $table_pk = "ID";
    /**
     * @var null 数据连接
     */
    protected $db = null;

    /**
     * Dao constructor.
     */
    public function __construct()
    {
        $f = new DbFactory();
        $this->db = $f->getInstance();
    }

    /**
     * 获取主键名称
     * @return string
     */
    public function getPK()
    {
        return $this->table_pk;
    }

    /**
     * 设置主键名称
     * @param $pk_name
     */
    public function setPK($pk_name)
    {
        $this->table_pk = $pk_name;
    }

    /**
     * 获取数据表记录数
     * @return int
     */
    public function getCount()
    {
        return $this->db->doSelectNumRows($this->table_name);
    }

    /**
     * 插入数据
     * @param $data
     * @return int
     */
    public function insertData($data)
    {
        return $this->db->doInsert($data, $this->table_name, true);
    }

    /**
     * 通过主键查询一行记录
     * @param $pk
     * @param array $fields
     * @return array|null
     */
    public function selectByPK($pk, $fields = ['*'])
    {
        if (is_numeric($pk)) {
            $where = "`" . $this->table_pk . '`=' . $pk;
        } else {
            $where = "`" . $this->table_pk . "`='" . $pk . "'";
        }
        return $this->db->doSelectOne($fields, $this->table_name, $where);
    }

    /**
     * 根据条件查询
     * @param array $fields
     * @param string $where
     * @param string $limit
     * @param string $order
     * @param string $group
     * @return array|null
     */
    public function select($fields = ['*'], $where = '', $limit = '', $order = '', $group = '')
    {
        return $this->db->doSelect($fields, $this->table_name, $where, $limit, $order, $group);
    }

    /**
     * 根据主键删除
     * @param $pk
     * @return bool
     */
    public function deleteByPK($pk)
    {
        if (is_numeric($pk)) {
            $where = "`" . $this->table_pk . '`=' . $pk;
        } else {
            $where = "`" . $this->table_pk . "`='" . $pk . "'";
        }
        return $this->db->doDelete($this->table_name, $where);
    }

    /**
     * 更新主键记录行
     * @param $pk
     * @param $data
     * @return bool|int
     */
    public function updateByPK($pk, $data)
    {
        if (is_numeric($pk)) {
            $where = "`" . $this->table_pk . '`=' . $pk;
        } else {
            $where = "`" . $this->table_pk . "`='" . $pk . "'";
        }
        return $this->db->doUpdate($data, $this->table_name, $where, true);
    }
}