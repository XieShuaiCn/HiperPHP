<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-15
 * Time: 下午8:31
 */

namespace Core\Base;

/**
 * 模型基类
 * Class Model
 * @package Core\Base
 */
class Model extends BaseObject
{
    protected $_class_name = "Model";
    protected $dao = null;
    protected $pk = null;
    protected $data = [];
    protected $data_change = [];

    /**
     * 变量是否存在
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        // TODO: Implement __get() method.
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }

    /**
     * 设置变量值
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        // TODO: Implement __get() method.
        if (!isset($this->data[$name]) || $this->data[$name] != $value) {
            $this->data[$name] = $value;
            $this->data_change[$name] = $value;
        }
    }

    /**
     * 是否设置某个变量
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        // TODO: Implement __isset() method.
        return isset($this->data[$name]);
    }

    /**
     * 取消设置某个变量
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        // TODO: Implement __unset() method.
        unset($this->data[$name]);
        unset($this->data_change[$name]);
    }

    /**
     * 跟据主键加载模型
     * @param $pk
     */
    public function load($pk)
    {
        if (!$this->dao instanceof Dao) {
            die("Dao should be initialized firstly.");
        }
        $fields = $this->dao->selectByPK($pk);
        $this->data = $fields;
        $this->pk = $fields[$this->dao->getPK()];
    }

    /**
     * 保存模型数据
     */
    public function save()
    {
        if (!$this->dao instanceof Dao) {
            die("Dao should be initialized firstly.");
        }
        //根据主键判断是插入还是更新
        if ($this->pk === null && count($this->data) > 0) {
            $this->dao->insertData($this->data);
        } elseif (count($this->data_change) > 0) {
            $this->dao->updateByPK($this->pk, $this->data_change);
        }
    }

    /**
     * 模型转数组
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * 模型转json
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->data);
    }
}