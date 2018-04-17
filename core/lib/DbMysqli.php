<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2017/8/5
 * Time: 17:05
 */

namespace Core\Lib;

class DbMysqli
{
    //数据库主机
    private $hostname = "";
    //数据库用户名
    private $user = "";
    //数据库密码
    private $password = "";
    //数据库名
    private $dbname = "";
    //端口
    private $port = 3306;
    //数据库连接句柄
    private $conn = null;
    //数据查询返回资源句柄
    private $query_handle = null;
    //刚执行的SQL语句
    private $sql = "";

    /**
     * db_mysqli.class constructor.
     * @param $host
     * @param $user
     * @param string $passwd
     * @param string $dbname
     * @param string $port
     * @param string $socket
     */
    function __construct($host, $user, $passwd = "", $dbname = "", $port = 3306)
    {
        $this->hostname = $host;
        $this->user = $user;
        $this->password = $passwd;
        $this->dbname = $dbname;
        $this->port = $port;
        $this->connect();
    }

    /**
     * db_mysqli.class destruct.
     */
    function __destruct()
    {
        $this->close();
    }

    /**
     * 链接数据库是否处于连接状态
     * @return bool
     */
    public function isConnected()
    {
        return ($this->conn != null && is_object($this->conn));
    }

    /**
     * 链接数据库
     * @return int 成功则返回0
     */
    public function connect()

    {
        if ($this->isConnected() == false) {
            $this->conn = mysqli_connect($this->hostname, $this->user, $this->password, $this->dbname, $this->port);
            return mysqli_connect_errno();
        }
        return 0;
    }

    /**
     * 关闭数据库
     * @return bool
     */
    public function close()
    {
        if ($this->isConnected()) {
            return mysqli_close($this->conn);
        }
    }

    /**
     * 设置数据库字符集
     * @param string $charset
     * @return bool
     */
    public function setCharset($charset = 'utf8')
    {
        if ($this->isConnected() == false) {
            return false;
        }
        return mysqli_set_charset($this->conn, $charset);
    }

    /**
     * 获取最近一次错误号
     * @return int
     */
    public function getLastErrNo()
    {
        return mysqli_errno($this->conn);
    }

    /**
     * 获取最近一次错误字符串
     * @return string
     */
    public function getLastError()
    {
        return mysqli_error($this->conn);
    }

    /**
     * 执行SQL语句
     * @param $sql
     * @return bool|mysqli_result|null
     */
    public function doExecute($sql)
    {
        if ($this->isConnected() == false) {
            if ($this->connect() != 0) {
                $this->sql = "";
                $this->query_handle = null;
                return null;
            }
        }
        $this->sql = $sql;
        //print_r($sql);
        $this->query_handle = mysqli_query($this->conn, $sql);
        return $this->query_handle;
    }

    /**
     * 获取上一次执行SQL语句
     * @return string
     */
    public function getLastSql()
    {
        return $this->sql;
    }

    /**
     * 选择默认数据库
     * @param $dbName
     * @return bool
     */
    public function setDbName($dbName)
    {
        if ($this->isConnected() == false) {
            return false;
        }
        $this->dbname = $dbName;
        return mysqli_select_db($this->conn, $this->dbname);
    }

    /**
     * 查询数据库
     * @param array $fields
     * @param string $table
     * @param string $where
     * @param string $limit
     * @param string $order
     * @param string $group
     * @return array|null
     */
    public function doSelect($fields, $table, $where = '', $limit = '', $order = '', $group = '')
    {
        $where = $where == '' ? '' : ' WHERE ' . $where;
        $order = $order == '' ? '' : ' ORDER BY ' . $order;
        $group = $group == '' ? '' : ' GROUP BY ' . $group;
        $limit = $limit == '' ? '' : ' LIMIT ' . $limit;

        array_walk($fields, array($this, 'addSpecialChar'));
        $field = implode(',', $fields);
        $sql = 'SELECT ' . $field . ' FROM `' . $table . '`' . $where . $group . $order . $limit;
        $this->doExecute($sql);
        if (!is_object($this->query_handle)) {
            return $this->query_handle;
        }

        $datalist = array();
        if (($rs = mysqli_fetch_all($this->query_handle, MYSQLI_ASSOC)) != false) {
            $datalist = $rs;
        }
        mysqli_free_result($this->query_handle);
        return $datalist;
    }

    /**
     * 查询数据库一行数据
     * @param array $fields
     * @param string $table
     * @param string $where
     * @param string $limit
     * @param string $order
     * @param string $group
     * @return array|null
     */
    public function doSelectOne($fields, $table, $where = '', $limit = '', $order = '', $group = '')
    {
        $where = $where == '' ? '' : ' WHERE ' . $where;
        $order = $order == '' ? '' : ' ORDER BY ' . $order;
        $group = $group == '' ? '' : ' GROUP BY ' . $group;
        $limit = $limit == '' ? ' LIMIT 1' : ' LIMIT ' . $limit;

        array_walk($fields, array($this, 'addSpecialChar'));
        $field = implode(',', $fields);
        $sql = 'SELECT ' . $field . ' FROM `' . $table . '`' . $where . $group . $order . $limit;
        $this->doExecute($sql);
        if (!is_object($this->query_handle)) {
            return $this->query_handle;
        }

        $datalist = mysqli_fetch_array($this->query_handle);
        mysqli_free_result($this->query_handle);
        return $datalist;
    }

    /**
     * 查询数据库，结果集行数
     * @return int
     */
    public function doSelectNumRows($table, $where = '', $group = '', $limit = '')
    {
        $where = $where == '' ? '' : ' WHERE ' . $where;
        $group = $group == '' ? '' : ' GROUP BY ' . $group;
        $limit = $limit == '' ? '' : ' LIMIT ' . $limit;

        $sql = 'SELECT count(*) FROM `' . $table . '`' . $where . $group . $limit;
        $this->doExecute($sql);

        return is_object($this->query_handle) ? (int)(mysqli_fetch_array($this->query_handle, MYSQLI_NUM)[0]) : 0;
    }

    /**
     * 执行添加记录操作
     * @param $data
     * @param $table
     * @param bool $return_insert_id
     * @param bool $replace
     * @return bool|int
     */
    public function doInsert($data, $table, $return_insert_id = false, $replace = false)
    {
        if (!is_array($data) || $table == '' || count($data) == 0) {
            return false;
        }

        $fielddata = array_keys($data);
        $valuedata = array_values($data);
        array_walk($fielddata, array($this, 'addSpecialChar'));
        array_walk($valuedata, array($this, 'escapeString'));
        $field = implode(',', $fielddata);
        $value = implode(',', $valuedata);

        $cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
        $sql = $cmd . ' `' . $table . '`(' . $field . ') VALUES (' . $value . ')';
        return $return_insert_id ?
            ($this->doExecute($sql) ? mysqli_insert_id($this->conn) : -1) :
            ($this->doExecute($sql));
    }

    /**
     * 获取最后一次添加记录的主键号
     * @return int
     */
    public function getInsertedId()
    {
        if ($this->isConnected() == false) {
            $this->connect();
        }
        return mysqli_insert_id($this->conn);
    }

    /**
     * 执行更新记录操作
     * @param $data string|array        要更新的数据内容，参数可以为数组也可以为字符串，建议数组。
     *                        为数组时数组key为字段值，数组值为数据取值
     *                        为字符串时[例：`name`='phpcms',`hits`=`hits`+1]。
     *                        为数组时[例: array('name'=>'phpcms','password'=>'123456')]
     *                        数组可使用array('name'=>'+=1', 'base'=>'-=1');程序会自动解析为`name` = `name` + 1, `base` = `base` - 1
     * @param $table string       数据表
     * @param $where string        更新数据时的条件
     * @param $return_affected_rows bool 是否返回影响行数
     * @return boolean|int
     */
    public function doUpdate($data, $table, $where = '', $return_affected_rows = false)
    {
        if ($table == '' or $where == '') {
            return false;
        }

        $where = ' WHERE ' . $where;
        $field = '';
        if (is_string($data) && $data != '') {
            $field = $data;
        } elseif (is_array($data) && count($data) > 0) {
            $fields = array();
            foreach ($data as $k => $v) {
                switch (substr($v, 0, 2)) {
                    case '+=':
                        $v = substr($v, 2);
                        if (is_numeric($v)) {
                            $fields[] = $this->addSpecialChar($k) . '=' . $this->addSpecialChar($k) . '+' . $this->escapeString($v, $k, false);
                        } else {
                            continue;
                        }

                        break;
                    case '-=':
                        $v = substr($v, 2);
                        if (is_numeric($v)) {
                            $fields[] = $this->addSpecialChar($k) . '=' . $this->addSpecialChar($k) . '-' . $this->escapeString($v, $k, false);
                        } else {
                            continue;
                        }
                        break;
                    default:
                        $fields[] = $this->addSpecialChar($k) . '=' . $this->escapeString($v);
                }
            }
            $field = implode(',', $fields);
        } else {
            return false;
        }

        $sql = 'UPDATE `' . $table . '` SET ' . $field . $where;
        return $return_affected_rows ?
            ($this->doExecute($sql) ? mysqli_affected_rows($this->conn) : 0) :
            ($this->doExecute($sql));
    }

    /**
     * 执行删除记录操作
     * @param $table  string       数据表
     * @param $where  string        删除数据条件
     * @return boolean/int
     */
    public function doDelete($table, $where)
    {
        if ($table == '') {
            return false;
        }
        if ($table != '') {
            $where = ' WHERE ' . $where;
        }
        $sql = 'DELETE FROM `' . $table . '`' . $where;
        return $this->doExecute($sql) ? mysqli_affected_rows($this->conn) : false;
    }

    /**
     * 执行清空表操作，不写入log
     * @param $table        数据表
     * @return boolean
     */
    public function doTruncate($table)
    {
        return $this->doExecute("TRUNCATE TABLE `" . $table . "`");
    }

    /**
     * 获取上一条SQL影响的行数
     * @return int
     */
    public function getAffectedRows()
    {
        if ($this->isConnected() == false) {
            $this->connect();
        }
        return mysqli_affected_rows($this->conn);
    }

    /**
     * 开始一个新的事务
     * @return bool 开始成功
     * @since 5.5.0
     */
    public function beginTransaction()
    {
        if ($this->isConnected() == false) {
            $this->connect();
        }
        if (PHP_MAJOR_VERSION < 5 || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 5)) {
            return $this->doExecute("START TRANSACTION");
        }
        return mysqli_begin_transaction($this->conn);
    }

    /**
     * 保存一个还原点
     * @param $name 还原点名称
     * @return bool 保存成功
     * @since 5.5.0
     */
    public function savePoint($name)
    {
        if ($this->isConnected() == false) {
            $this->connect();
        }
        if (PHP_MAJOR_VERSION < 5 || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 5)) {
            return $this->doExecute("SAVEPOINT " . $name);
        }
        return mysqli_savepoint($this->conn, $name);
    }

    /**
     * 回滚一个事务
     * @return bool 回滚成功
     */
    public function rollback($point_name = null)
    {
        if (is_string($point_name) && $point_name != "") {
            return $this->doExecute("ROLLBACK TO " . $point_name);
        } else {
            return $this->doExecute("ROLLBACK");
        }
        //return mysqli_rollback($this->conn);
    }

    /**
     * 设置自动提交事务
     * @param bool $mode true|false,当设置为false时，必须手动调用commit()才能生效
     * @return bool
     */
    public function setAutoCommit($mode = true)
    {
        if ($this->isConnected() == false) {
            $this->connect();
        }
        return mysqli_autocommit($this->conn, $mode);
    }

    /**
     * 提交当前事务
     * @return bool 提交成功
     */
    public function commit()
    {
        if ($this->isConnected() == false) {
            $this->connect();
        }
        return mysqli_commit($this->conn);
    }

    /**
     * 获取数据表主键
     * @param $table        dbTable
     * @return array
     */
    public function getPrimaryKey($table)
    {
        $this->doExecute("SHOW COLUMNS FROM $table");
        while ($r = mysqli_fetch_row($this->query_handle)) {
            if ($r['Key'] == 'PRI') break;
        }
        return $r['Field'];
    }

    /**
     * 获取表字段
     * @param $table        dbTable
     * @return array
     */
    public function getTableFields($table)
    {
        $fields = array();
        $this->doExecute("SHOW COLUMNS FROM $table");
        while ($r = mysqli_fetch_row($this->query_handle)) {
            $fields[$r['Field']] = $r['Type'];
        }
        return $fields;
    }

    /**
     * 获取数据库版本
     * @return string
     */
    public function getDbInfo()
    {
        if ($this->isConnected() == false) {
            $this->connect();
        }
        return mysqli_get_server_info($this->conn);
    }

    /**
     * 获取数据库版本
     * @return int
     */
    public function getDbVersion()
    {
        if ($this->isConnected() == false) {
            $this->connect();
        }
        return mysqli_get_server_version($this->conn);
    }

    /**
     * 对字段两边加反引号，以保证数据库安全
     * @param &$value 数组值(地址引用)
     */
    public function addSpecialChar(&$value)
    {
        if ('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos($value, '`')) {
            //不处理包含* 或者 使用了sql方法。
        } else {
            $value = '`' . trim($value) . '`';
        }
        if (preg_match("/\b(select|insert|update|delete)\b/i", $value)) {
            $value = preg_replace("/\b(select|insert|update|delete)\b/i", '', $value);
        }
        return $value;
    }

    /**
     * 对字段值两边加引号，以保证数据库安全
     * @param &$value 数组值(地址引用)
     * @param $key 键名，array_walk时必须的参数
     * @param bool $quotation 是否转义
     */
    public function escapeString(&$value, $key = "", $quotation = true)
    {
        if ($quotation) {
            $value = "'" . $value . "'";
        } else {

        }
        return $value;
    }
}