<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/5/1
 * Time: 16:24
 */

namespace Core\Module;

/**
 * 模板类
 * Class Template
 * @package Core\Module
 */
class Template
{
    /**
     * @var array 模板配置
     */
    protected $config = [
        'tpl_dir' => APP_ROOT . '/template',
        'tpl_begin' => '<',
        'tpl_end' => '>',
    ];
    /**
     * @var string 模板起始结束标记
     */
    protected $tpl_begin = '<';
    protected $tpl_end = '>';
    /**
     * @var array 模板变量的数值
     */
    protected $data = [];
    /**
     * @var \Core\Base\Cache 系统缓存
     */
    protected $cache = null;
    /**
     * @var string 模板文件名
     */
    protected $tpl = null;
    /**
     * @var string 模板名加密后的ID
     */
    protected $tpl_id = null;
    /**
     * @var string 模板在缓存中的键名
     */
    protected $tpl_cache_name = null;
    /**
     * @var string 模板的预处理生成文件
     */
    protected $tpl_tmp_file = null;
    /**
     * @var array 模板中嵌套的文件信息
     */
    protected $tpl_include_files = [];

    /**
     * Template constructor.
     * @param $tpl_file
     */
    public function __construct($tpl_file)
    {
        //读取配置
        if (is_file(APP_ROOT . '/config/template.conf.php')) {
            $this->config = array_merge($this->config, include CONFIG_ROOT . '/template.config.php');
        }
        //加载常用配置
        $this->tpl_begin = $this->config['tpl_begin'];
        $this->tpl_end = $this->config['tpl_end'];
        //模板文件路径
        $this->tpl = $this->config['tpl_dir'] . '/' . $tpl_file;
        $this->tpl_id = md5($tpl_file);
        $this->tpl_cache_name = 'tpl_' . $this->tpl_id;
        if (!is_dir(CACHE_ROOT . '/tpl_tmp/')) {
            mkdir(CACHE_ROOT . '/tpl_tmp/');
        }
        $this->tpl_tmp_file = CACHE_ROOT . '/tpl_tmp/' . $this->tpl_id . ".php";
        $this->cache = CacheFactory::getInstance("core");
    }

    /**
     * 判断是否被缓存，判断缓存有效性
     * @return bool
     */
    protected function isCached()
    {
        //检测文件是否存在
        if (!is_file($this->tpl_tmp_file)) {
            return false;
        }
        //验证修改日期
        $t1 = filemtime($this->tpl_tmp_file);
        $t2 = filemtime($this->tpl);
        if ($t2 > $t1) {//模板文件比缓存修改日期新则为无效
            return false;
        }
        //是否有缓存记录
        if (!$this->cache->existKey($this->tpl_cache_name)) {
            return false;
        }
        return true;
    }

    /**
     * 给模板变量赋值
     * @param string|array $name
     * @param null string $value
     */
    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } elseif ($value !== null) {
            $this->data[$name] = $value;
        }
    }

    /**
     * 获取模板的临时文件
     * @return null|string
     */
    public function getTmpFile()
    {
        return $this->tpl_tmp_file;
    }

    /**
     * 预编译模板，将模板渲染成php文件
     * @return bool
     */
    public function render()
    {
        //检测是否有缓存
        $cached = $this->isCached();
        if (!$cached) {
            $tpl_content = file_get_contents($this->tpl);
            //分析模板嵌套的文件
            $reg_ret = preg_match_all(
                "/" . $this->tpl_begin . '\[include ([\w\W]+?)\]' . $this->tpl_end . "/i",
                $tpl_content, $reg_matches);
            if ($reg_ret != false) {
                //记录嵌套模板信息
                for ($i = 0; $i < $reg_ret; ++$i) {
                    $this->tpl_include_files [] = [
                        'tpl_string' => $reg_matches[0][$i],//模板匹配的字符串
                        'file_tpl' => $reg_matches[1][$i]//捕获分组1的字符串
                    ];
                }
                //将模板嵌套的分析结果存入缓存
                $this->cache->setHashValue($this->tpl_cache_name, 'include_files', $this->tpl_include_files);
            }
        } else {
            //读取模板嵌套结果
            $v = $this->cache->getHashValue($this->tpl_cache_name, 'include_files');
            if ($v !== false) {
                $this->tpl_include_files = array_merge($this->tpl_include_files, $v);
            }
        }
        //迭代render子模板
        foreach ($this->tpl_include_files as &$f) {
            $t = new Template($f['file_tpl']);
            $t->assign($this->data);
            $t->render();
            $f['file_tmp'] = $t->getTmpFile();
        }
        //开始预编译模板
        if (!$cached) {
            /*//编译模板变量,此操作放在控制分析时解决
            $tpl_content = preg_replace(
                '/' . $this->tpl_begin . '\[var.(\w+?)\]' . $this->tpl_end . '/',
                '<?=$this->data["\1"]?>', $tpl_content);*/
            //编译模板变量的简单写法
            $tpl_content = preg_replace(
                '/' . $this->tpl_begin . '\{(\w+?)\}' . $this->tpl_end . '/',
                '<?=$this->data["\1"]?>', $tpl_content);
            //编译模板嵌套
            foreach ($this->tpl_include_files as $f) {
                $tpl_content = str_replace(
                    $f['tpl_string'],
                    '<?php include "' . $f['file_tmp'] . '"; ?>',
                    $tpl_content);
            }
            //编译逻辑控制语句
            $tsm = new TemplateStateMachine();
            $tpl_content = preg_replace_callback(
                '/' . $this->tpl_begin . '\[(\S+?)((?:\s+[\w\W]+?)*)\]' . $this->tpl_end . '/',
                [$tsm, 'analysisPreg'], $tpl_content);
            file_put_contents($this->tpl_tmp_file, $tpl_content);
        }
        return true;
    }

    /**
     * 显示模板页面
     */
    public function display()
    {
        $this->render();
        try {
            include $this->tpl_tmp_file;
        } catch (\Exception $e) {

        }
    }
}

/**
 * 模板状态机类，用于解析模板
 * Class TemplateStateMachine
 * @package Core\Module
 */
class TemplateStateMachine
{
    /**
     * 状态码
     */
    const STATE_UNKNOWN = -1;
    const STATE_NORMAL = 0;
    const STATE_IF = 1;
    const STATE_IF_ELIF = 2;
    const STATE_IF_ELSE = 3;
    const STATE_CONTINUE = 4;
    const STATE_BREAK = 5;
    const STATE_FOR = 6;

    /**
     * @var array 状态栈
     */
    private $state = [self::STATE_NORMAL];
    /**
     * @var array 局部变量
     */
    private $variable = [];

    /**
     * 判断模板的语法是否规则合法
     * @return bool
     */
    public function isRegular()
    {
        return count($this->state) === 1 && $this->state[0] === self::STATE_NORMAL;
    }

    /**
     * 分析正则结果，用作正则的回调函数
     * @param array $matches
     * @return string
     */
    public function analysisPreg($matches)
    {
        $current = end($this->state);
        $new_state = self::STATE_UNKNOWN;
        //取出两端空白，统一小写
        $matches[1] = strtolower(trim($matches[1]));
        $rpl_value = '';
        switch ($matches[1]) {
            case 'foreach':
            case 'while':
            case 'for':
                $condition = $this->getFor(isset($matches[2]) ? $matches[2] : null);
                $rpl_value = '<?php ' . $condition . ' {?>';
                $this->state[] = self::STATE_FOR;
                break;
            case 'if':
                $condition = $this->getIfCondition(isset($matches[2]) ? $matches[2] : null);
                $rpl_value = '<?php if (' . $condition . ') {?>';
                $this->state[] = self::STATE_IF;
                break;
            case 'elseif':
                if ($current === self::STATE_IF ||
                    $current === self::STATE_IF_ELIF) {
                    //分析if条件
                    $condition = $this->getIfCondition(isset($matches[2]) ? $matches[2] : null);
                    $rpl_value = '<?php } elseif (' . $condition . ') {?>';
                    $this->state[count($this->state) - 1] = self::STATE_IF_ELIF;
                }
                break;
            case 'else':
                if ($current == self::STATE_IF ||
                    $current == self::STATE_IF_ELIF) {
                    $rpl_value = '<?php } else { ?>';
                    $this->state[count($this->state) - 1] = self::STATE_IF_ELSE;
                }
                break;
            case 'endfor':
                //var_dump($this->state);
                if ($current === self::STATE_FOR) {
                    $rpl_value = '<?php }?>';
                    array_pop($this->state);
                }
                break;
            case 'endif':
                //var_dump($this->state);
                if ($current === self::STATE_IF ||
                    $current === self::STATE_IF_ELIF ||
                    $current === self::STATE_IF_ELSE) {
                    $rpl_value = '<?php }?>';
                    array_pop($this->state);
                }
                break;
            case 'break':
                $rpl_value = '<?php break;?>';
                break;
            case 'continue':
                $rpl_value = '<?php continue;?>';
                break;
            default:
                //变量
                if (strpos($matches[1], 'var.') === 0) {
                    $rpl_value = '<?=' . $this->getVariableString($matches[1]) . '?>';
                }
                break;
        }
        return $rpl_value;
    }

    /**
     * 根据条件字符串构造for语句
     * @param string $str_condition for的条件字符串
     * @return string
     */
    protected function getFor($str_condition)
    {
        if (empty($str_condition)) {//没有参数时为死循环
            $condition = 'for ( ; ; )';
        } //解析for范围，v in 1 to 99 | v in arr | arr as v
        elseif (preg_match('/\s*([\w\.\'"]+)\s*(in|as)\s*([\w\.\'"]+)\s*(to\s*([\w\.\'"]+)\s*)?/', $str_condition, $sub_matches)) {
            //var_dump($sub_matches);
            //注册新变量
            if ($sub_matches[2] == 'in') {
                if(in_array($sub_matches[1], $this->variable)) {
                    $this->variable[] = $sub_matches[1];
                }
                if (isset($sub_matches[5])) {//v in 1 to 99
                    $condition = 'for ($' . $sub_matches[1] . '=' . $this->getVariableString($sub_matches[3])
                        . ';$' . $sub_matches[1] . '<=' . $this->getVariableString($sub_matches[5])
                        . ';++$' . $sub_matches[1] . ')';
                } else {//v in arr
                    $condition = 'foreach (' . $this->getVariableString($sub_matches[3]) . ' as $' . $sub_matches[1] . ')';
                }
            } else {//arr as v
                if(in_array($sub_matches[3], $this->variable)) {
                    $this->variable[] = $sub_matches[3];
                }
                $condition = 'foreach (' . $this->getVariableString($sub_matches[1]) . ' as $' . $sub_matches[3] . ')';
            }
        }//无法解析，原样返回
        else {
            $condition = 'for (' . $str_condition . ')';
        }
        return $condition;
    }

    /**
     * 根据if的条件字符串构造if条件字符串的php格式
     * @param string $str_condition if的条件字符串
     * @return string
     */
    protected function getIfCondition($str_condition)
    {
        $condition = '';
        if (empty($str_condition)) {
            $condition = 'true';
        } elseif (preg_match('/\s*([\w\.\'"]+)\s*(?:([\w\W]+?)\s*([\w\.\'"]+)\s*)?/',
            $str_condition, $sub_matches)) {
            //var_dump($sub_matches);
            $arg1_arg = $this->getVariableString($sub_matches[1]);
            //单变量if
            if (!isset($sub_matches[2])) {
                $condition = $arg1_arg;
            } //条件if
            else {
                $arg2_arg = $this->getVariableString($sub_matches[3]);
                $condition = $arg1_arg . $sub_matches[2] . $arg2_arg;
            }
        } else {
            $condition = $str_condition;
        }
        return $condition;
    }

    /**
     * 构造变量的php字符串
     * @param string $arg_str 变量表达
     * @return string
     */
    protected function getVariableString($arg_str)
    {
        $arg1 = explode('.', $arg_str);
        if ($arg1[0] == 'var') {
            //var_dump($arg1, $this->variable);
            if (isset($arg1[1])) {
                //模板局部变量
                if (in_array($arg1[1], $this->variable, true)) {
                    //组合变量访问形式，也可能是数组
                    $arg_str = '$' . $arg1[1];
                } //控制器传参来的变量
                else {
                    $arg_str = '$this->data[\'' . $arg1[1] . '\']';
                }
                //若访问的是数组，继续加[]
                for ($i = 2; $i < count($arg1); ++$i) {
                    $arg_str .= '[\'' . $arg1[1] . '\']';
                }
            }
        } else {//加单引号，数字和布尔变字符串后也能使用
            $arg_str = "'" . $arg1[0] . "'";
        }
        return $arg_str;
    }
}