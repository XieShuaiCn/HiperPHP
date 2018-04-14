<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2017/8/5
 * Time: 17:05
 */

class db_mysqli
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
    function __destruct ()
    {
        $this->close();
    }

    /**
     * 链接数据库是否处于连接状态
     * @return bool
     */
    public function is_connected()
    {
        return ( $this->conn != null && is_object($this->conn));
    }

    /**
     * 链接数据库
     * @return int 成功则返回0
     */
    public function connect()
    {
        if($this->is_connected() == false){
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
        if($this->is_connected()){
            return mysqli_close($this->conn);
        }
    }

    /**
     * 设置数据库字符集
     * @param string $charset
     * @return bool
     */
    public function set_charset($charset = 'utf8')
    {
        if($this->is_connected() == false){
            return false;
        }
        return mysqli_set_charset($this->conn, $charset);
    }

    /**
     * 获取最近一次错误号
     * @return int
     */
    public function get_last_errno()
    {
        return mysqli_errno($this->conn);
    }

    /**
     * 获取最近一次错误字符串
     * @return string
     */
    public function get_last_error()
    {
        return mysqli_error($this->conn);
    }

    /**
     * 执行SQL语句
     * @param $sql
     * @return bool|mysqli_result|null
     */
    public function execute($sql)
    {
        if($this->is_connected() == false){
            $this->query_handle = null;
            return null;
        }
        $this->sql = $sql;
        $this->query_handle = mysqli_query($this->conn, $sql);
        return $this->query_handle;
    }

    /**
     * 获取上一次执行SQL语句
     * @return string
     */
     public function get_last_sql()
    {
        return $this->sql;
    }

    /**
     * 选择默认数据库
     * @param $dbName
     * @return bool
     */
    public function set_db($dbName)
    {
        if($this->is_connected() == false)
        {
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
    public function select($fields, $table, $where = '', $limit = '', $order = '', $group = '') {
        $where = $where == '' ? '' : ' WHERE '.$where;
        $order = $order == '' ? '' : ' ORDER BY '.$order;
        $group = $group == '' ? '' : ' GROUP BY '.$group;
        $limit = $limit == '' ? '' : ' LIMIT '.$limit;

        array_walk($fields, array($this, 'add_special_char'));
        $field = implode (',', $fields);
        $sql = 'SELECT '.$field.' FROM `'.$table.'`'.$where.$group.$order.$limit;
        $this->execute($sql);
        if(!is_object($this->query_handle)) {
            return $this->query_handle;
        }

        $datalist = array();
        if(($rs = mysqli_fetch_all($this->query_handle,MYSQLI_BOTH)) != false) {
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
    public function select_one($fields, $table, $where = '', $limit = '', $order = '', $group = '') {
        $where = $where == '' ? '' : ' WHERE '.$where;
        $order = $order == '' ? '' : ' ORDER BY '.$order;
        $group = $group == '' ? '' : ' GROUP BY '.$group;
        $limit = $limit == '' ? '' : ' LIMIT '.$limit;

        array_walk($fields, array($this, 'add_special_char'));
        $field = implode (',', $fields);
        $sql = 'SELECT '.$field.' FROM `'.$table.'`'.$where.$group.$order.$limit;
        $this->execute($sql);
        if(!is_object($this->query_handle)) {
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
    public function select_num_rows($table, $where = '', $limit = '', $order = '', $group = '') {
        $where = $where == '' ? '' : ' WHERE '.$where;
        $order = $order == '' ? '' : ' ORDER BY '.$order;
        $group = $group == '' ? '' : ' GROUP BY '.$group;
        $limit = $limit == '' ? '' : ' LIMIT '.$limit;

        $sql = 'SELECT count(*) FROM `'.$table.'`'.$where.$group.$order.$limit;
        $this->execute($sql);

        return is_object($this->query_handle) ? (int)(mysqli_fetch_array($this->query_handle, MYSQLI_NUM)[0]) : 0;
    }

    /**
     * 执行添加记录操作
     * @param $data 		array,数组key为字段值,value为数据取值
     * @param $table 		dbTable
     * @param $return_insert_id     boolean:ret_id
     * @param $replace       boolean:relace
     * @return boolean/int
     */
    public function insert($data, $table, $return_insert_id = false, $replace = false) {
        if(!is_array( $data ) || $table == '' || count($data) == 0) {
            return false;
        }

        $fielddata = array_keys($data);
        $valuedata = array_values($data);
        array_walk($fielddata, array($this, 'add_special_char'));
        array_walk($valuedata, array($this, 'escape_string'));
        $field = implode (',', $fielddata);
        $value = implode (',', $valuedata);

        $cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
        $sql = $cmd.' `'.$table.'`('.$field.') VALUES ('.$value.')';
        return $return_insert_id ?
            ($this->execute($sql) ? mysqli_insert_id($this->conn) : -1) :
            ($this->execute($sql));
    }

    /**
     * 获取最后一次添加记录的主键号
     * @return int
     */
    public function get_insert_id() {
        if($this->is_connected() == false) {
            $this->connect();
        }
        return mysqli_insert_id($this->conn);
    }

    /**
     * 执行更新记录操作
     * @param $data 		要更新的数据内容，参数可以为数组也可以为字符串，建议数组。
     * 						为数组时数组key为字段值，数组值为数据取值
     * 						为字符串时[例：`name`='phpcms',`hits`=`hits`+1]。
     *						为数组时[例: array('name'=>'phpcms','password'=>'123456')]
     *						数组可使用array('name'=>'+=1', 'base'=>'-=1');程序会自动解析为`name` = `name` + 1, `base` = `base` - 1
     * @param $table 		数据表
     * @param $where    	更新数据时的条件
     * @param $return_affected_rows 是否返回影响行数
     * @return boolean/int
     */
    public function update($data, $table, $where = '', $return_affected_rows = false) {
        if($table == '' or $where == '') {
            return false;
        }

        $where = ' WHERE '.$where;
        $field = '';
        if(is_string($data) && $data != '') {
            $field = $data;
        } elseif (is_array($data) && count($data) > 0) {
            $fields = array();
            foreach($data as $k=>$v) {
                switch (substr($v, 0, 2)) {
                    case '+=':
                        $v = substr($v,2);
                        if (is_numeric($v)) {
                            $fields[] = $this->add_special_char($k).'='.$this->add_special_char($k).'+'.$this->escape_string($v, $k, false);
                        } else {
                            continue;
                        }

                        break;
                    case '-=':
                        $v = substr($v,2);
                        if (is_numeric($v)) {
                            $fields[] = $this->add_special_char($k).'='.$this->add_special_char($k).'-'.$this->escape_string($v, $k, false);
                        } else {
                            continue;
                        }
                        break;
                    default:
                        $fields[] = $this->add_special_char($k).'='.$this->escape_string($v);
                }
            }
            $field = implode(',', $fields);
        } else {
            return false;
        }

        $sql = 'UPDATE `'.$table.'` SET '.$field.$where;
        return $return_affected_rows ?
            ($this->execute($sql) ? mysqli_affected_rows($this->conn) : 0) :
            ($this->execute($sql));
    }

    /**
     * 执行删除记录操作
     * @param $table 		数据表
     * @param $where 		删除数据条件
     * @return boolean/int
     */
    public function delete($table, $where) {
        if ($table == '') {
            return false;
        }
        if ($table != '') {
            $where = ' WHERE ' . $where;
        }
        $sql = 'DELETE FROM `'.$table.'`'.$where;
        return $this->execute($sql) ? mysqli_affected_rows($this->conn) : false;
    }

    /**
     * 执行清空表操作，不写入log
     * @param $table 		数据表
     * @return boolean
     */
    public function truncate($table) {
        return $this->execute("TRUNCATE TABLE `".$table."`");
    }

    /**
     * 获取上一条SQL影响的行数
     * @return int
     */
    public function get_affected_rows()
    {
        if($this->is_connected() == false)
        {
            $this->connect();
        }
        return mysqli_affected_rows($this->conn);
    }

    /**
     * 获取数据表主键
     * @param $table 		dbTable
     * @return array
     */
    public function get_primary_key($table) {
        $this->execute("SHOW COLUMNS FROM $table");
        while($r = mysqli_fetch_row($this->query_handle)) {
            if($r['Key'] == 'PRI') break;
        }
        return $r['Field'];
    }

    /**
     * 获取表字段
     * @param $table 		dbTable
     * @return array
     */
    public function get_fields($table) {
        $fields = array();
        $this->execute("SHOW COLUMNS FROM $table");
        while($r = mysqli_fetch_row($this->query_handle)) {
            $fields[$r['Field']] = $r['Type'];
        }
        return $fields;
    }

    /**
     * 获取数据库版本
     * @return string
     */
    public function get_db_version()
    {
        if($this->is_connected() == false)
        {
            $this->connect();
        }
        return mysqli_get_server_info($this->conn);
    }

    /**
     * 获取数据库版本
     * @return int
     */
    public function get_db_version_int()
    {
        if($this->is_connected() == false)
        {
            $this->connect();
        }
        return mysqli_get_server_version($this->conn);
    }

    /**
     * 对字段两边加反引号，以保证数据库安全
     * @param &$value 数组值(地址引用)
     */
    public function add_special_char(&$value) {
        if('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos ( $value, '`')) {
            //不处理包含* 或者 使用了sql方法。
        } else {
            $value = '`'.trim($value).'`';
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
     * @param bool $quotation    是否转义
     */
    public function escape_string(&$value, $key = "", $quotation = true) {
        if($quotation){
            $value = "'".$value."'";
        } else {

        }
        return $value;
    }
}