<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-15
 * Time: 下午8:30
 */

namespace Core\Base;

use Core\Module\DbFactory;

class Dao extends BaseObject
{
    protected $_class_name = "Dao";
    /**
     * @var string 数据表名称
     */
    protected $tale_name = null;
    /**
     * @var string 数据表主键
     */
    protected $table_pk = "ID";
    /**
     * @var null 数据连接
     */
    protected $db = null;

    public function __construct()
    {
        $f = new DbFactory();
        $this->db = $f->getInstance();
    }

    public function getPK()
    {
        return $this->table_pk;
    }

    public function setPK($pk_name)
    {
        $this->table_pk = $pk_name;
    }

    public function getCount()
    {
        return $this->db->doSelectNumRows($this->tale_name);
    }

    public function insertData($data)
    {
        return $this->db->doInsert($data, $this->tale_name, true);
    }

    public function selectByPK($pk, $fields = ['*'])
    {
        if (is_numeric($pk)) {
            $where = "`" . $this->table_pk . '`=' . $pk;
        } else {
            $where = "`" . $this->table_pk . "`='" . $pk . "'";
        }
        return $this->db->doSelect($fields, $this->tale_name, $where);
    }

    public function select($fields = ['*'], $where = '', $limit = '', $order = '', $group = '')
    {
        return $this->db->doSelect($fields, $this->tale_name, $where, $limit, $order, $group);
    }

    public function deleteByPK($pk)
    {
        if (is_numeric($pk)) {
            $where = "`" . $this->table_pk . '`=' . $pk;
        } else {
            $where = "`" . $this->table_pk . "`='" . $pk . "'";
        }
        return $this->db->doDelete($this->tale_name, $where);
    }

    public function updateByPK($pk, $data)
    {
        if (is_numeric($pk)) {
            $where = "`" . $this->table_pk . '`=' . $pk;
        } else {
            $where = "`" . $this->table_pk . "`='" . $pk . "'";
        }
        return $this->db->doUpdate($data, $this->tale_name, $where, true);
    }
}